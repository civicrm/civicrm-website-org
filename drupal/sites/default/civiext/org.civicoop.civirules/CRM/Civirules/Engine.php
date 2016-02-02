<?php
/**
 * Class for CiviRules engine
 *
 * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
 * @license AGPL-3.0
 */

class CRM_Civirules_Engine {

  const QUEUE_NAME = 'org.civicoop.civirules.action';

  /**
   * Trigger a rule.
   *
   * The trigger will check the conditions and if conditions are valid then the actions are executed
   *
   * @param CRM_Civirules_Trigger $trigger
   * @param object CRM_Civirules_TriggerData_TriggerData $triggerData
   * @return bool true when conditions are valid; false when conditions are not valid
   * @access public
   * @static
   */
  public static function triggerRule(CRM_Civirules_Trigger $trigger, CRM_Civirules_TriggerData_TriggerData $triggerData) {
    try {
      $triggerData->setTrigger($trigger);
      $isRuleValid = self::areConditionsValid($triggerData);

      if ($isRuleValid) {
        self::logRule($triggerData);
        self::executeActions($triggerData);
        return true;
      }
    } catch (Exception $e) {
      $message = "Error on {file} (Line {line})\r\n\r\n{exception_message}";
      $context = array();
      $context['line'] = $e->getLine();
      $context['file'] = $e->getFile();
      $context['exception_message'] = $e->getMessage();
      CRM_Civirules_Utils_LoggerFactory::logError("Failed to execute rule",  $message, $triggerData, $context);
    }
    return false;
  }

  /**
   * Method to execute the actions
   *
   * @param object CRM_Civirules_TriggerData_TriggerData $triggerData
   * @access protected
   * @static
   */
  protected static function executeActions(CRM_Civirules_TriggerData_TriggerData $triggerData) {
    $actionParams = array(
      'rule_id' => $triggerData->getTrigger()->getRuleId(),
    );
    $ruleActions = CRM_Civirules_BAO_RuleAction::getValues($actionParams);
    foreach ($ruleActions as $ruleAction) {
      self::executeAction($triggerData, $ruleAction);
    }
  }

  /**
   * Method to execute a single action
   *
   * @param object CRM_Civirules_TriggerData_TriggerData $triggerData
   * @param array $ruleAction
   * @access protected
   * @static
   */
  protected static function executeAction(CRM_Civirules_TriggerData_TriggerData $triggerData, $ruleAction) {
    $object = CRM_Civirules_BAO_Action::getActionObjectById($ruleAction['action_id'], true);
    if (!$object) {
      return;
    }

    $object->setRuleActionData($ruleAction);

    //determine if the action should be executed with a delay
    $delay = self::getActionDelay($ruleAction, $object);
    if ($delay instanceof DateTime) {
      self::delayAction($delay, $object, $triggerData);
    } else {
      //there is no delay so process action immediatly
      $object->processAction($triggerData);
    }
  }

  /**
   * Process delayed actions
   *
   * @param int $maxRunTime
   * @return array
   */
  public static function processDelayedActions($maxRunTime=30) {
    $queue = CRM_Queue_Service::singleton()->create(array(
      'type' => 'Civirules',
      'name' => self::QUEUE_NAME,
      'reset' => false, //do not flush queue upon creation
    ));

    $returnValues = array();

    //retrieve the queue
    $runner = new CRM_Queue_Runner(array(
      'title' => ts('Process delayed civirules actions'), //title fo the queue
      'queue' => $queue, //the queue object
      'errorMode'=> CRM_Queue_Runner::ERROR_CONTINUE, //continue on error otherwise the queue will hang
    ));

    $stopTime = time() + $maxRunTime; //stop executing next item after 30 seconds
    while((time() < $stopTime) && $queue->numberOfItems() > 0) {
      $result = $runner->runNext(false);
      $returnValues[] = $result;

      if (!$result['is_continue']) {
        break;
      }
    }

    return $returnValues;
  }

  /**
   * Executes a delayed action
   *
   * @param \CRM_Queue_TaskContext $ctx
   * @param \CRM_Civirules_Action $action
   * @param \CRM_Civirules_TriggerData_TriggerData $triggerData
   * @return bool
   */
  public static function executeDelayedAction(CRM_Queue_TaskContext $ctx, CRM_Civirules_Action $action, CRM_Civirules_TriggerData_TriggerData $triggerData) {
    try {
      if ($action->ignoreConditionsOnDelayedProcessing()) {
        $processAction = true;
      } else {
        $processAction = self::areConditionsValid($triggerData);
      }

      if ($processAction) {
        $action->processAction($triggerData);
      }
    } catch (Exception $e) {
      CRM_Civirules_Utils_LoggerFactory::logError("Failed to execute delayed action",  $e->getMessage(), $triggerData);
    }
    return true;
  }

  /**
   * Save an action into a queue for delayed processing
   *
   * @param \DateTime $delayTo
   * @param \CRM_Civirules_Action $action
   * @param \CRM_Civirules_TriggerData_TriggerData $triggerData
   */
  protected static function delayAction(DateTime $delayTo, CRM_Civirules_Action $action, CRM_Civirules_TriggerData_TriggerData $triggerData) {
    $queue = CRM_Queue_Service::singleton()->create(array(
      'type' => 'Civirules',
      'name' => self::QUEUE_NAME,
      'reset' => false, //do not flush queue upon creation
    ));

    //create a task with the action and eventData as parameters
    $task = new CRM_Queue_Task(
      array('CRM_Civirules_Engine', 'executeDelayedAction'), //call back method
      array($action, $triggerData) //parameters
    );

    //save the task with a delay
    $dao              = new CRM_Queue_DAO_QueueItem();
    $dao->queue_name  = $queue->getName();
    $dao->submit_time = CRM_Utils_Time::getTime('YmdHis');
    $dao->data        = serialize($task);
    $dao->weight      = 0; //weight, normal priority
    $dao->release_time = $delayTo->format('YmdHis');
    $dao->save();
  }

  /**
   * Returns false when action could not be delayed or return a DateTime
   * This DateTime object holds the date and time till when the action should be delayed
   *
   * The delay is calculated by a separate delay class. See CRM_Civirules_DelayDelay
   *
   * @param $ruleAction
   * @param CRM_Civirules_Action $actionObject
   * @return bool|\DateTime
   */
  protected static function getActionDelay($ruleAction, CRM_Civirules_Action $actionObject) {
    $delayedTo = new DateTime();
    $now = new DateTime();
    if (!empty($ruleAction['delay'])) {
      $delayClass = unserialize(($ruleAction['delay']));
      if ($delayClass instanceof CRM_Civirules_Delay_Delay) {
        $delayedTo = $delayClass->delayTo($delayedTo);
      }
    }

    $actionDelayedTo = $actionObject->delayTo($delayedTo);
    if ($actionDelayedTo instanceof DateTime) {
      if ($now < $actionDelayedTo) {
        return $actionDelayedTo;
      }
      return false;
    } elseif ($delayedTo instanceof DateTime and $now < $delayedTo) {
      return $delayedTo;
    }
    return false;
  }

  /**
   * Method to check if all conditions are valid
   *
   * @param object CRM_Civirules_TriggerData_TriggerData $triggerData
   * @return bool
   * @access protected
   * @static
   */
  protected static function areConditionsValid(CRM_Civirules_TriggerData_TriggerData $triggerData) {
    $isValid = true;
    $firstCondition = true;

    $conditionParams = array(
      'rule_id' => $triggerData->getTrigger()->getRuleId(),
    );
    $ruleConditions = CRM_Civirules_BAO_RuleCondition::getValues($conditionParams);
    foreach ($ruleConditions as $ruleConditionId => $ruleCondition) {
      $isConditionValid = self::checkCondition($ruleCondition, $triggerData);
      if ($firstCondition) {
        $isValid = $isConditionValid ? true : false;
        $firstCondition = false;
      } elseif ($ruleCondition['condition_link'] == 'AND') {
        if ($isConditionValid && $isValid) {
          $isValid = true;
        } else {
          $isValid = false;
        }
      } elseif ($ruleCondition['condition_link'] == 'OR'){
        if ($isConditionValid || $isValid) {
          $isValid = true;
        } else {
          $isValid = false;
        }
      } else {
        $isValid = false; //we should never reach this statement
      }
    }
    return $isValid;
  }

  /**
   * Method to check condition
   *
   * @param array $ruleCondition
   * @param object CRM_Civirules_TriggerData_TriggerData $triggerData
   * @return bool
   * @access protected
   * @static
   */
  protected static function checkCondition($ruleCondition, CRM_Civirules_TriggerData_TriggerData $triggerData) {
    $condition = CRM_Civirules_BAO_Condition::getConditionObjectById($ruleCondition['condition_id'], false);
    if (!$condition) {
      return false;
    }
    $condition->setRuleConditionData($ruleCondition);
    $isValid = $condition->isConditionValid($triggerData);
    return $isValid;
  }

  /**
   * This function writes a record to the log table to indicate that this rule for this trigger is triggered
   *
   * The data this function stores is required by the cron type events.
   * @todo: think of a better handling for cron type events
   *
   * @param CRM_Civirules_TriggerData_TriggerData $triggerData
   */
  protected static function logRule(CRM_Civirules_TriggerData_TriggerData $triggerData) {
    $sql = "INSERT INTO `civirule_rule_log` (`rule_id`, `contact_id`, `log_date`) VALUES (%1, %2, NOW())";
    $params[1] = array($triggerData->getTrigger()->getRuleId(), 'Integer');
    $params[2] = array($triggerData->getContactId(), 'Integer');
    CRM_Core_DAO::executeQuery($sql, $params);
  }

}