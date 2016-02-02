<?php
/**
 * Class for CiviRules condition last contribution xxx days ago
 *
 * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
 * @license AGPL-3.0
 */

class CRM_CivirulesConditions_Contribution_LastContribution extends CRM_CivirulesConditions_Generic_ValueComparison {

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

    $params[1] = array($completed_status_id, 'Integer');
    $params[2] = array($contact_id, 'Integer');

    $last_date = CRM_Core_DAO::singleValueQuery("SELECT MAX(`receive_date`) FROM `civicrm_contribution` WHERE `contribution_status_id` = %1 AND `contact_id` = %2", $params);
    if ($last_date) {
      $last_date = new DateTime($last_date);
      return $last_date->diff(new DateTime('now'))->days;
    }
    return false; //undefined contribution date
  }

  /**
   * Returns a user friendly text explaining the condition params
   * e.g. 'Older than 65'
   *
   * @return string
   * @access public
   */
  public function userFriendlyConditionParams() {
    switch ($this->getOperator()) {
      case '=':
        $label =  'Last contribution is %1 days ago';
        break;
      case '>':
        $label =  'Last contribution is more than %1 days ago';
        break;
      case '<':
        $label =  'Last contribution is less than %1 days ago';
        break;
      case '>=':
        $label =  'Last contribution is more than %1 days ago or is %1 days ago';
        break;
      case '<=':
        $label =  'Last contribution is less than %1 days ago or is %1 days ago';
        break;
      case '!=':
        $label =  'Last contribution is not %1 days ago';
        break;
      default:
        return '';
        break;
    }
    return ts($label, array(1 => $this->getComparisonValue()));
  }

  /**
   * Returns an array with possible operators
   *
   * @return array
   */
  public function getOperators() {
    return array(
      '=' => ts('Last contribution is n days ago'),
      '!=' => ts('Last contribution is not n days ago'),
      '>' => ts('Last contribution is more than n days ago'),
      '<' => ts('Last contribution is less than n days ago'),
      '>=' => ts('Last contribution is more than n days ago or is n days ago'),
      '<=' => ts('Last contribution is less than n days ago or is n days ago'),
    );
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

}