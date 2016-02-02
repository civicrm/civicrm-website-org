<?php

class CRM_CivirulesConditions_Contribution_PaidBy extends CRM_Civirules_Condition {

  private $conditionParams = array();

  /**
   * Method to set the Rule Condition data
   *
   * @param array $ruleCondition
   * @access public
   */
  public function setRuleConditionData($ruleCondition) {
    parent::setRuleConditionData($ruleCondition);
    $this->conditionParams = array();
    if (!empty($this->ruleCondition['condition_params'])) {
      $this->conditionParams = unserialize($this->ruleCondition['condition_params']);
    }
  }

  /**
   * Method to determine if the condition is valid
   *
   * @param CRM_Civirules_TriggerData_TriggerData $triggerData
   * @return bool
   */

  public function isConditionValid(CRM_Civirules_TriggerData_TriggerData $triggerData) {
    $isConditionValid = FALSE;
    $contribution = $triggerData->getEntityData('Contribution');
    switch ($this->conditionParams['operator']) {
      case 0:
        if ($contribution['payment_instrument_id'] == $this->conditionParams['payment_instrument_id']) {
          $isConditionValid = TRUE;
        }
      break;
      case 1:
        if ($contribution['payment_instrument_id'] != $this->conditionParams['payment_instrument_id']) {
          $isConditionValid = TRUE;
        }
      break;
    }
    return $isConditionValid;
  }

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
  public function getExtraDataInputUrl($ruleConditionId) {
    return CRM_Utils_System::url('civicrm/civirule/form/condition/contribution_paidby/', 'rule_condition_id='.$ruleConditionId);
  }

  /**
   * Returns a user friendly text explaining the condition params
   * e.g. 'Older than 65'
   *
   * @return string
   * @access public
   */
  public function userFriendlyConditionParams() {
    $operator = null;
    if ($this->conditionParams['operator'] == 0) {
      $operator = 'equals';
    }
    if ($this->conditionParams['operator'] == 1) {
      $operator = 'is not equal to';
    }
    $paymentInstrumentName = $this->getPaymentInstrumentName();
    if (!empty($paymentInstrumentName)) {
      return 'Paid by '.$operator.' '.$paymentInstrumentName;
    }
    return '';
  }

  /**
   * Method to get payment instrument
   *
   * @return string|null
   * @access protected
   */
  protected function getPaymentInstrumentName() {
    $paymentInstrumentName = null;
    if (!empty($this->conditionParams['payment_instrument_id'])) {
      $optionGroupParams = array(
        'name' => 'payment_instrument',
        'return' => 'id');
      try {
        $optionGroupId = civicrm_api3('OptionGroup', 'Getvalue', $optionGroupParams);
        $optionValueParams = array(
          'option_group_id' => $optionGroupId,
          'is_active' => 1,
          'value' => $this->conditionParams['payment_instrument_id'],
          'return' => 'label');
        try {
          $paymentInstrumentName = civicrm_api3('OptionValue', 'Getvalue', $optionValueParams);
        } catch (CiviCRM_API3_Exception $ex) {}

      } catch (CiviCRM_API3_Exception $ex) {}
    }
    return $paymentInstrumentName;
  }

  /**
   * Returns an array with required entity names
   *
   * @return array
   * @access public
   */
  public function requiredEntities() {
    return array('Contribution');
  }

}