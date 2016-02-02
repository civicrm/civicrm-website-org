<?php
/**
 * Abstract Class for CiviRules action
 *
 * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
 * @license AGPL-3.0
 */

abstract class CRM_Civirules_Action {

  protected $ruleAction = array();

  protected $action = array();

  /**
   * Process the action
   *
   * @param CRM_Civirules_TriggerData_TriggerData $triggerData
   * @access public
   */
  abstract public function processAction(CRM_Civirules_TriggerData_TriggerData $triggerData);

  /**
   * You could override this method to create a delay for your action
   *
   * You might have a specific action which is Send Thank you and which
   * includes sending thank you SMS to the donor but only between office hours
   *
   * If you have a delay you should return a DateTime object with a future date and time
   * for when this action should be processed.
   *
   * If you don't have a delay you should return false
   *
   * @param DateTime $date the current scheduled date/time
   * @return bool|DateTime
   */
  public function delayTo(DateTime $date) {
    return false;
  }

  /**
   * Method to set RuleActionData
   *
   * @param $ruleAction
   * @access public
   */
  public function setRuleActionData($ruleAction) {
    $this->ruleAction = array();
    if (is_array($ruleAction)) {
      $this->ruleAction = $ruleAction;
    }
  }

  /**
   * Method to set actionData
   *
   * @param $action
   * @access public
   */
  public function setActionData($action) {
    $this->action = $action;
  }

  /**
   * Convert parameters to an array of parameters
   *
   * @return array
   * @access protected
   */
  protected function getActionParameters() {
    $params = array();
    if (!empty($this->ruleAction['action_params'])) {
      $params = unserialize($this->ruleAction['action_params']);
    }
    return $params;
  }

  /**
   * Returns wether we should ignore rechecking of the conditions when an action
   * is executed with a delay
   *
   * @return bool
   */
  public function ignoreConditionsOnDelayedProcessing() {
    return $this->ruleAction['ignore_condition_with_delay'] ? true : false;
  }

  /**
   * Returns a redirect url to extra data input from the user after adding a action
   *
   * Return false if you do not need extra data input
   *
   * @param int $ruleActionId
   * @return bool|string
   * $access public
   */
  abstract public function getExtraDataInputUrl($ruleActionId);

  /**
   * Returns a user friendly text explaining the condition params
   * e.g. 'Older than 65'
   *
   * @return string
   * @access public
   */
  public function userFriendlyConditionParams() {
    return '';
  }

  /**
   * Logs a message to the logger
   *
   * @param $message
   * @param \CRM_Civirules_TriggerData_TriggerData|NULL $triggerData
   * @param string $level Should be one of \Psr\Log\LogLevel
   */
  protected function logAction($message, CRM_Civirules_TriggerData_TriggerData $triggerData=null, $level=\Psr\Log\LogLevel::INFO) {
    $context = array();
    $context['message'] = $message;
    $context['rule_id'] = $this->ruleAction['rule_id'];
    $rule = new CRM_Civirules_BAO_Rule();
    $rule->id = $this->ruleAction['rule_id'];
    $context['rule_title'] = '';
    if ($rule->find(true)) {
      $context['rule_title'] = $rule->label;
    }
    $context['rule_action_id'] = $this->ruleAction['id'];
    $context['action_label'] = CRM_Civirules_BAO_Action::getActionLabelWithId($this->ruleAction['action_id']);
    $context['action_parameters'] = $this->userFriendlyConditionParams();
    $context['contact_id'] = $triggerData ? $triggerData->getContactId() : - 1;
    $msg = "{action_label} (ID: {rule_action_id})\r\n\r\n{message}\r\n\r\nRule: '{rule_title}' with id {rule_id}";
    if ($context['contact_id'] > 0) {
      $msg .= "\r\nFor contact: {contact_id}";
    }
    CRM_Civirules_Utils_LoggerFactory::log($msg, $context, $level);
  }


}