<?php

/**
 * Class CRM_CivirulesConditions_Contribution_RecurringEndDate
 *
 * This CiviRule condition will check if the end date of the recurring contribution is set or not set
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @link http://redmine.civicoop.org/projects/civirules/wiki/Tutorial_create_a_more_complicated_condition_with_its_own_form_processing
 */

class CRM_CivirulesConditions_Contribution_RecurringEndDate extends CRM_Civirules_Condition {

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
    $recurring = $triggerData->getEntityData('ContributionRecur');
    if ($this->conditionParams['end_date'] == 0 && empty($recurring['end_date'])) {
      $isConditionValid = TRUE;
    }
    if ($this->conditionParams['end_date'] == 1 && !empty($recurring['end_date'])) {
      $isConditionValid = TRUE;
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
    return CRM_Utils_System::url('civicrm/civirule/form/condition/contribution_recurringenddate/', 'rule_condition_id='.$ruleConditionId);
  }

  /**
   * Returns a user friendly text explaining the condition params
   * e.g. 'Older than 65'
   *
   * @return string
   * @access public
   */
  public function userFriendlyConditionParams() {
    if ($this->conditionParams['end_date'] == 1) {
      $endDateString = 'is set';
    } else {
      $endDateString = 'is not set';
    }
    return 'End Date of Recurring Contribution '.$endDateString;
  }

  /**
   * Returns an array with required entity names
   *
   * @return array
   * @access public
   */
  public function requiredEntities() {
    return array('ContributionRecur');
  }
}