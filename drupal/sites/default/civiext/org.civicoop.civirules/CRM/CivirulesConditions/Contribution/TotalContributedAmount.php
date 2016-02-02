<?php

class CRM_CivirulesConditions_Contribution_TotalContributedAmount extends CRM_CivirulesConditions_Generic_ValueComparison {

  /**
   * Returns value of the field
   *
   * @param object CRM_Civirules_TriggerData_TriggerData $triggerData
   * @return mixed
   * @access protected
   */
  protected function getFieldValue(CRM_Civirules_TriggerData_TriggerData $triggerData) {
    $completed_status_id = CRM_Core_OptionGroup::getValue('contribution_status', 'completed', 'name');
    $contact_id = $triggerData->getContactId();

    $sql = "SELECT SUM(`total_amount`)
            FROM `civicrm_contribution`
            WHERE `contribution_status_id` = %1 AND `contact_id` = %2";

    $params[1] = array($completed_status_id, 'Integer');
    $params[2] = array($contact_id, 'Integer');

    $periodStartDate = CRM_CivirulesConditions_Utils_Period::convertPeriodToStartDate($this->conditionParams);
    $periodEndDate = CRM_CivirulesConditions_Utils_Period::convertPeriodToEndDate($this->conditionParams);
    if ($periodStartDate) {
      $sql .= " AND DATE(`receive_date`) >= '".$periodStartDate->format('Y-m-d')."'";
    }
    if ($periodEndDate) {
      $sql .= " AND DATE(`receive_date`) <= '".$periodEndDate->format('Y-m-d')."'";
    }

    $total_amount = (float) CRM_Core_DAO::singleValueQuery($sql, $params);
    return $total_amount;
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
    return CRM_Utils_System::url('civicrm/civirule/form/condition/contribution_totalcontributedamount/', 'rule_condition_id='.$ruleConditionId);
  }

  /**
   * Returns a user friendly text explaining the condition params
   * e.g. 'Older than 65'
   *
   * @return string
   * @access public
   */
  public function userFriendlyConditionParams() {
    $userFriendlyConditionParams = parent::userFriendlyConditionParams();
    $period = CRM_CivirulesConditions_Utils_Period::userFriendlyConditionParams($this->conditionParams);

    return ts('Total amount').' '.$period.' '.$userFriendlyConditionParams;
  }

}