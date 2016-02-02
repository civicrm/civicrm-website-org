<?php

/**
 * Class CRM_CivirulesConditions_Contribution_CountRecurring
 *
 * This CiviRule condition will check for the xth contribution resulting from a recurring contribution
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @link http://redmine.civicoop.org/projects/civirules/wiki/Tutorial_create_a_more_complicated_condition_with_its_own_form_processing
 */

class CRM_CivirulesConditions_Contribution_CountRecurring extends CRM_Civirules_Condition {

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
    /*
     * retrieve count of completed contributions for donor where recurring_contribution_id is not empty
     */
    $query = 'SELECT COUNT(*) AS recurringContributions FROM civicrm_contribution
WHERE contact_id = %1 AND civicrm_contribution.contribution_recur_id > %2 AND contribution_status_id = %3';
    $params = array(
      1 => array($contribution['contact_id'], 'Positive'),
      2 => array(0, 'Positive'),
      3 => array(CRM_Civirules_Utils::getContributionStatusIdWithName('Completed'), 'String'));
    $dao = CRM_Core_DAO::executeQuery($query, $params);
    if ($dao->fetch()) {

      switch ($this->conditionParams['operator']) {
        case 1:
          if ($dao->recurringContributions != $this->conditionParams['no_of_recurring']) {
            $isConditionValid = TRUE;
          }
        break;
        case 2:
          if ($dao->recurringContributions > $this->conditionParams['no_of_recurring']) {
            $isConditionValid = TRUE;
          }
        break;
        case 3:
          if ($dao->recurringContributions >= $this->conditionParams['no_of_recurring']) {
          $isConditionValid = TRUE;
        }
        break;
        case 4:
          if ($dao->recurringContributions < $this->conditionParams['no_of_recurring']) {
          $isConditionValid = TRUE;
        }
        break;
        case 5:
          if ($dao->recurringContributions <= $this->conditionParams['no_of_recurring']) {
          $isConditionValid = TRUE;
        }
        break;
        default:
          if ($dao->recurringContributions == $this->conditionParams['no_of_recurring']) {
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
    return CRM_Utils_System::url('civicrm/civirule/form/condition/contribution_countrecurring/', 'rule_condition_id='.$ruleConditionId);
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
        $operator = 'more than';
        break;
      case 3:
        $operator = 'more than or equal to';
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
    return 'Number of recurring contribution collections '.$operator.' '.$this->conditionParams['no_of_recurring'];
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