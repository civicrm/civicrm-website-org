<?php

/**
 * Class CRM_CivirulesConditions_Contribution_DistinctContributingDay
 *
 * This CiviRule condition will check for the xth distinct contributing day for the donor.
 * All contributions on the same date will count as 1.
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @link http://redmine.civicoop.org/projects/civirules/wiki/Tutorial_create_a_more_complicated_condition_with_its_own_form_processing
 */

class CRM_CivirulesConditions_Contribution_DistinctContributingDay extends CRM_Civirules_Condition {

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

  public function isConditionValid(CRM_Civirules_TriggerData_TriggerData $triggerData)
  {
    $isConditionValid = FALSE;
    $contact_id = $triggerData->getContactId();
    /*
     * retrieve count of contributions for donor grouped by extracted YMD from receive_date
     */
    $query = 'SELECT COUNT(DISTINCT CONCAT(EXTRACT(YEAR FROM receive_date),
EXTRACT(MONTH FROM receive_date), EXTRACT(DAY FROM receive_date))) AS distinctDates
FROM civicrm_contribution WHERE contact_id = %1 AND civicrm_contribution.contribution_status_id = %2';
    $params = array(
      1 => array($contact_id, 'Positive'),
      2 => array(CRM_Civirules_Utils::getContributionStatusIdWithName('Completed'), 'Positive'));

    $periodStartDate = CRM_CivirulesConditions_Utils_Period::convertPeriodToStartDate($this->conditionParams);
    $periodEndDate = CRM_CivirulesConditions_Utils_Period::convertPeriodToEndDate($this->conditionParams);
    if ($periodStartDate) {
      $query .= " AND DATE(`receive_date`) >= '".$periodStartDate->format('Y-m-d')."'";
    }
    if ($periodEndDate) {
      $query .= " AND DATE(`receive_date`) <= '".$periodEndDate->format('Y-m-d')."'";
    }

    $dao = CRM_Core_DAO::executeQuery($query, $params);
    if ($dao->fetch()) {

      switch ($this->conditionParams['operator']) {
        case 1:
          if ($dao->distinctDates != $this->conditionParams['no_of_days']) {
            $isConditionValid = TRUE;
          }
        break;
        case 2:
          if ($dao->distinctDates > $this->conditionParams['no_of_days']) {
            $isConditionValid = TRUE;
          }
        break;
        case 3:
          if ($dao->distinctDates >= $this->conditionParams['no_of_days']) {
          $isConditionValid = TRUE;
        }
        break;
        case 4:
          if ($dao->distinctDates < $this->conditionParams['no_of_days']) {
          $isConditionValid = TRUE;
        }
        break;
        case 5:
          if ($dao->distinctDates <= $this->conditionParams['no_of_days']) {
          $isConditionValid = TRUE;
        }
        break;
        default:
          if ($dao->distinctDates == $this->conditionParams['no_of_days']) {
            $isConditionValid = TRUE;
          }
        break;
      }
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
    return CRM_Utils_System::url('civicrm/civirule/form/condition/contribution_distinctcontributingday/', 'rule_condition_id='.$ruleConditionId);
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
    switch ($this->conditionParams['operator']) {
      case 1:
        $operator = 'is not equal to';
        break;
      case 2:
        $operator = 'bigger than';
        break;
      case 3:
        $operator = 'bigger than or equal to';
        break;
      case 4:
        $operator = 'less than';
        break;
      case 5:
        $operator = 'less than or equal to';
        break;
      default:
        $operator = 'is equal to';
        break;
    }

    $period = CRM_CivirulesConditions_Utils_Period::userFriendlyConditionParams($this->conditionParams);

    return 'Distinct number of contributing days '.$operator.' '.$this->conditionParams['no_of_days'].' '.$period;
  }
}