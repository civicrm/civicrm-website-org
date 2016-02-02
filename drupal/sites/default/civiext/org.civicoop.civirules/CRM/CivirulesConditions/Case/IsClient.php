<?php

class CRM_CivirulesConditions_Case_IsClient extends CRM_Civirules_Condition {

  public function isConditionValid(CRM_Civirules_TriggerData_TriggerData $triggerData) {
    $relationship = $triggerData->getEntityData('Relationship');
    if (empty($relationship)) {
      return true;
    }
    return false;
  }

  public function getExtraDataInputUrl($ruleConditionId) {
    return false;
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