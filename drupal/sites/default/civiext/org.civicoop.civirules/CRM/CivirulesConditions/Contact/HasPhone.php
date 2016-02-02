<?php

class CRM_CivirulesConditions_Contact_HasPhone extends CRM_Civirules_Condition {

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
   * This method returns true or false when an condition is valid or not
   *
   * @param CRM_Civirules_TriggerData_TriggerData $triggerData
   * @return bool
   * @access public
   * @abstract
   */
  public function isConditionValid(CRM_Civirules_TriggerData_TriggerData $triggerData) {
    $phoneType = $this->conditionParams['phone_type'];
    $apiParams['contact_id'] = $triggerData->getContactId();
    if ($phoneType) {
      $apiParams['phone_type_id'] = $phoneType;
    }
    $count = civicrm_api3('Phone', 'getCount', $apiParams);
    if ($count) {
      return true;
    }
    return false;
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
    return CRM_Utils_System::url('civicrm/civirule/form/condition/contact_hasphone/', 'rule_condition_id='.$ruleConditionId);
  }

  /**
   * Returns a user friendly text explaining the condition params
   * e.g. 'Older than 65'
   *
   * @return string
   * @access public
   */
  public function userFriendlyConditionParams() {
    $phoneType = $this->conditionParams['phone_type'];
    $phoneTypes = CRM_Core_OptionGroup::values('phone_type', false, false, false, false, 'label', false);
    $phoneTypeLabel = ts('Any phone type');
    if ($phoneType && isset($phoneTypes[$phoneType])) {
      $phoneTypeLabel = ts('Phone type is %1', array(
        1 => $phoneTypes[$phoneType],
      ));
    }
    $locationType = $this->conditionParams['location_type'];
    $locationTypeLabel = ts('Any location');
    if ($locationType) {
      try {
        $locationTypeLabel = ts('Location is %1', array(
          1 => civicrm_api3('LocationType', 'getvalue', array('id' => $locationType, 'return' => 'display_name'))
        ));
      } catch (Exception $e) {
        //do nothing
      }
    }
    return $phoneTypeLabel . ts(' and ').$locationTypeLabel;
  }

}