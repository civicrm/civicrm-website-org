<?php
/**
 * Class for CiviRule Condition DonorIsRecurring
 *
 * Passes if donor has any active recurring contributions
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 */

class CRM_CivirulesConditions_Contribution_DonorIsRecurring extends CRM_Civirules_Condition {

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
   * Method is mandatory and checks if the condition is met
   *
   * @param CRM_Civirules_TriggerData_TriggerData $triggerData
   * @return bool
   * @access public
   */
  public function isConditionValid(CRM_Civirules_TriggerData_TriggerData $triggerData) {
    $contactId = $triggerData->getContactId();
    $donorHasAny = FALSE;
    $recurringParams = array(
      'contact_id' => $contactId,
      'is_test' => 0);
    try {
      $foundRecurring = civicrm_api3('ContributionRecur', 'Get', $recurringParams);
      foreach ($foundRecurring['values'] as $recurring) {
        if (CRM_Civirules_Utils::endDateLaterThanToday($recurring['end_date']) == TRUE || !isset($recurring['end_date'])) {
          $donorHasAny = TRUE;
        }
      }
      if ($donorHasAny) {
        if ($this->conditionParams['has_recurring']) {
          $isConditionValid = TRUE;
        } else {
          $isConditionValid = FALSE;
        }
      } else {
        if ($this->conditionParams['has_recurring']) {
          $isConditionValid = FALSE;
        } else {
          $isConditionValid = TRUE;
        }
      }
    } catch (CiviCRM_API3_Exception $ex) {
      if ($this->conditionParams['has_recurring']) {
        $isConditionValid = FALSE;
      } else {
        $isConditionValid = TRUE;
      }
    }
    return $isConditionValid;
  }

  /**
   * Method is mandatory, in this case no additional data input is required
   * so it returns FALSE
   *
   * @param int $ruleConditionId
   * @return bool
   * @access public
   */
  public function getExtraDataInputUrl($ruleConditionId) {
    return CRM_Utils_System::url('civicrm/civirule/form/condition/contribution_donorisrecurring/', 'rule_condition_id='.$ruleConditionId);
  }

  /**
   * Returns a user friendly text explaining the condition params
   *
   * @return string
   * @access public
   */
  public function userFriendlyConditionParams() {
    if ($this->conditionParams['has_recurring'] == 0) {
      return 'Donor has no active recurring contributions today';
    } else {
      return 'Donor has active recurring contributions today';
    }
  }

  /**
   * Returns an array with required entity names
   *
   * @return array
   * @access public
   */
  public function requiredEntities() {
    return array('Contact');
  }
}