<?php

class CRM_CivirulesConditions_Case_CaseType extends CRM_Civirules_Condition {

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
    $case = $triggerData->getEntityData('Case');
    switch ($this->conditionParams['operator']) {
      case 0:
        if ($case['case_type_id'] == $this->conditionParams['case_type_id']) {
          $isConditionValid = TRUE;
        }
        break;
      case 1:
        if ($case['case_type_id'] != $this->conditionParams['case_type_id']) {
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
    return CRM_Utils_System::url('civicrm/civirule/form/condition/case/casetype', 'rule_condition_id='
      .$ruleConditionId);
  }

  /**
   * Returns a user friendly text explaining the condition params
   * e.g. 'Older than 65'
   *
   * @return string
   * @access public
   */
  public function userFriendlyConditionParams() {
    try {
      $caseTypes = civicrm_api3('CaseType', 'Get', array('is_active' => 1));
      $operator = null;
      if ($this->conditionParams['operator'] == 0) {
        $operator = 'equals';
      }
      if ($this->conditionParams['operator'] == 1) {
        $operator = 'is not equal to';
      }
      foreach ($caseTypes['values'] as $caseType) {
        if ($caseType['id'] == $this->conditionParams['case_type_id']) {
          return "Case Type ".$operator." ".$caseType['title'];
        }
      }
    } catch (CiviCRM_API3_Exception $ex) {}
    return '';
  }

  /**
   * Returns an array with required entity names
   *
   * @return array
   * @access public
   */
  public function requiredEntities() {
    return array('Case');
  }

}