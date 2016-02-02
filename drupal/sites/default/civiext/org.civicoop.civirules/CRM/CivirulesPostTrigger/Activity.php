<?php

class CRM_CivirulesPostTrigger_Activity extends CRM_Civirules_Trigger_Post {

  /**
   * Returns an array of entities on which the trigger reacts
   *
   * @return CRM_Civirules_TriggerData_EntityDefinition
   */
  protected function reactOnEntity() {
    return new CRM_Civirules_TriggerData_EntityDefinition($this->objectName, $this->objectName, $this->getDaoClassName(), 'Activity');
  }

  /**
   * Return the name of the DAO Class. If a dao class does not exist return an empty value
   *
   * @return string
   */
  protected function getDaoClassName() {
    return 'CRM_Activity_DAO_Activity';
  }

  /**
   * Trigger a rule for this trigger
   *
   * @param $op
   * @param $objectName
   * @param $objectId
   * @param $objectRef
   */
  public function triggerTrigger($op, $objectName, $objectId, $objectRef) {
    $triggerData = $this->getTriggerDataFromPost($op, $objectName, $objectId, $objectRef);

    //trigger for activity trigger for every source_contact_id, target_contact_id and assignee_contact_id
    $activityContact = new CRM_Activity_BAO_ActivityContact();
    $activityContact->activity_id = $objectId;
    $activityContact->find();
    while($activityContact->fetch()) {
      $data = array();
      CRM_Core_DAO::storeValues($activityContact, $data);
      $triggerData->setEntityData('ActivityContact', $data);

      CRM_Civirules_Engine::triggerRule($this, clone $triggerData);
    }
  }

  /**
   * Returns an array of additional entities provided in this trigger
   *
   * @return array of CRM_Civirules_TriggerData_EntityDefinition
   */
  protected function getAdditionalEntities() {
    $entities = parent::getAdditionalEntities();
    $entities[] = new CRM_Civirules_TriggerData_EntityDefinition('ActivityContact', 'ActivityContact', 'CRM_Activity_DAO_ActivityContact' , 'ActivityContact');
    return $entities;
  }

}