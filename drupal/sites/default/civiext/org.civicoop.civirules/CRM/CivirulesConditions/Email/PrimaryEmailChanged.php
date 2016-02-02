<?php
/**
 * Class for CiviRules Primary Email Changed condition (extending generic FieldChanged)
 *
 * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
 * @license AGPL-3.0
 */

class CRM_CivirulesConditions_Email_PrimaryEmailChanged extends CRM_CivirulesConditions_Generic_FieldChanged {

  /**
   * Returns name of entity
   *
   * @return string
   * @access protected
   */
  protected function getEntity() {
    return 'Email';
  }

  /**
   * Returns name of the field
   *
   * @return string
   * @access protected
   */
  protected function getField() {
    return 'email';
  }

  /**
   * Method to check if the condition is valid
   *
   * @param object CRM_Civirules_TriggerData_TriggerData $triggerData
   * @return bool
   * @access public
   */
  public function isConditionValid(CRM_Civirules_TriggerData_TriggerData $triggerData) {
    $isValid = parent::isConditionValid($triggerData);
    if ($isValid) {
      $data = $triggerData->getEntityData($this->getEntity());
      if (!empty($data['is_primary'])) {
        $isValid = true;
      } else {
        $isValid = false;
      }
    }
    return $isValid;
  }
}