<?php
/**
 * Class for CiviRule Condition Activity Record Type
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 */

class CRM_CivirulesConditions_Activity_RecordType extends CRM_Civirules_Condition {

  private $conditionParams = array();

  public function getExtraDataInputUrl($ruleConditionId) {
    return CRM_Utils_System::url('civicrm/civirule/form/condition/activity_contact_record_type/',
      'rule_condition_id='.$ruleConditionId);
  }

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
   * Method to check if the condition is valid, will check if the contact
   * has an activity of the selected type
   *
   * @param object CRM_Civirules_TriggerData_TriggerData $triggerData
   * @return bool
   * @access public
   */
  public function isConditionValid(CRM_Civirules_TriggerData_TriggerData $triggerData) {
    $ActivityContact = $triggerData->getEntityData('ActivityContact');
    if ($ActivityContact['record_type_id'] == $this->conditionParams['record_type_id']) {
      return true;
    }
    return false;
  }
  /**
   * Returns a user friendly text explaining the condition params
   * e.g. 'Older than 65'
   *
   * @return string
   * @access public
   */
  public function userFriendlyConditionParams() {
    $activityTypeLabel = CRM_Civirules_Utils::getOptionLabelWithValue(CRM_Civirules_Utils::getOptionGroupIdWithName('activity_contacts'), $this->conditionParams['record_type_id']);
    if (!empty($activityTypeLabel)) {
      return ts('For all %1', array(1 => $activityTypeLabel));
    }
    return '';
  }

  /**
   * Returns an array with required entity names
   *
   * @return array
   * @access public
   */
  public function requiredEntities() {
    return array(
      'Activity',
      'ActivityContact'
    );
  }
}