<?php
/**
 * Abstract Class for CiviRules condition
 *
 * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
 * @license AGPL-3.0
 */

abstract class CRM_Civirules_Condition {

  protected $ruleCondition = array();

  /**
   * Method to set RuleConditionData
   *
   * @param $ruleCondition
   * @access public
   */
  public function setRuleConditionData($ruleCondition) {
    $this->ruleCondition = array();
    if (is_array($ruleCondition)) {
      $this->ruleCondition = $ruleCondition;
    }
  }

  /**
   * This method returns true or false when an condition is valid or not
   *
   * @param CRM_Civirules_TriggerData_TriggerData $triggerData
   * @return bool
   * @access public
   * @abstract
   */
  public abstract function isConditionValid(CRM_Civirules_TriggerData_TriggerData $triggerData);

  /**
   * Returns a redirect url to extra data input from the user after adding a condition
   *
   * Return false if you do not need extra data input
   *
   * @param int $ruleConditionId
   * @return bool|string
   * @access public
   * @abstract
   */
  abstract public function getExtraDataInputUrl($ruleConditionId);

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
   * Returns an array with required entity names
   *
   * @return array
   * @access public
   */
  public function requiredEntities() {
    return array();
  }

  /**
   * Logs a message to the logger
   *
   * @param $message
   * @param \CRM_Civirules_TriggerData_TriggerData|NULL $triggerData
   * @param string $level Should be one of \Psr\Log\LogLevel
   */
  protected function logCondition($message, CRM_Civirules_TriggerData_TriggerData $triggerData=null, $level=\Psr\Log\LogLevel::INFO) {
    $context = array();
    $context['message'] = $message;
    $context['rule_id'] = $this->ruleCondition['rule_id'];
    $rule = new CRM_Civirules_BAO_Rule();
    $rule->id = $this->ruleCondition['rule_id'];
    $context['rule_title'] = '';
    if ($rule->find(true)) {
      $context['rule_title'] = $rule->label;
    }
    $context['rule_condition_id'] = $this->ruleCondition['id'];
    $context['condition_label'] = CRM_Civirules_BAO_Condition::getConditionLabelWithId($this->ruleCondition['condition_id']);
    $context['condition_parameters'] = $this->userFriendlyConditionParams();
    $context['contact_id'] = $triggerData ? $triggerData->getContactId() : - 1;
    $msg = "{condition_label} (ID: {rule_condition_id})\r\n\r\n{message}\r\n\r\nRule: '{rule_title}' with id {rule_id}";
    if ($context['contact_id'] > 0) {
      $msg .= "\r\nFor contact: {contact_id}";
    }
    CRM_Civirules_Utils_LoggerFactory::log($msg, $context, $level);
  }



}