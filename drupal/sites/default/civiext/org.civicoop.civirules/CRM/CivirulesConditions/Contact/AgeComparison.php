<?php
/**
 * Class for CiviRules AgeComparison (extending generic ValueComparison)
 *
 * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
 * @license AGPL-3.0
 */

class CRM_CivirulesConditions_Contact_AgeComparison extends CRM_CivirulesConditions_Generic_ValueComparison {

  /**
   * Returns value of the field
   *
   * @param object CRM_Civirules_TriggerData_TriggerData $triggerData
   * @return mixed
   * @access protected
   */
  protected function getFieldValue(CRM_Civirules_TriggerData_TriggerData $triggerData) {
    $sourceBirthDate = civicrm_api3('Contact', 'getvalue', array('id' => $triggerData->getContactId(), 'return' => 'birth_date'));
    if ($sourceBirthDate) {
      $birthDate = new DateTime($sourceBirthDate);
      return $birthDate->diff(new DateTime('now'))->y;
    }
    return false; //undefined birth date
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
        $label =  'Age is %1';
        break;
      case '>':
        $label =  'Age is older than %1';
        break;
      case '<':
        $label =  'Age is younger than %1';
        break;
      case '>=':
        $label =  'Age is %1 or older than %1';
        break;
      case '<=':
        $label =  'Age is %1 or younger than %1';
        break;
      case '!=':
        $label =  'Age is not %1';
        break;
      default:
        return '';
        break;
    }
    return ts($label, array(1 => $this->getComparisonValue()));
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