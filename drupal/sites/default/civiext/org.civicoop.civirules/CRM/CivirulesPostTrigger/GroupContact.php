<?php

class CRM_CivirulesPostTrigger_GroupContact extends CRM_Civirules_Trigger_Post {

  /**
   * Returns an array of entities on which the trigger reacts
   *
   * @return CRM_Civirules_TriggerData_EntityDefinition
   */
  protected function reactOnEntity() {
    return new CRM_Civirules_TriggerData_EntityDefinition($this->objectName, $this->objectName, $this->getDaoClassName(), 'GroupContact');
  }

  /**
   * Return the name of the DAO Class. If a dao class does not exist return an empty value
   *
   * @return string
   */
  protected function getDaoClassName() {
    return 'CRM_Contact_DAO_GroupContact';
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
    //in case of GroupContact $objectRef consist of an array of contactIds
    //so convert this array to group contact objects
    //we do this by a query on the group_contact table to retrieve the latest records for this group and contact
    $sql = "SELECT MAX(`id`), `group_id`, `contact_id`, `status`, `location_id`, `email_id`
            FROM `civicrm_group_contact`
            WHERE `group_id` = %1 AND `contact_id` IN (".implode(", ", $objectRef).")
            GROUP BY `contact_id`";
    $params[1] = array($objectId, 'Integer');
    $dao = CRM_Core_DAO::executeQuery($sql, $params, true, 'CRM_Contact_DAO_GroupContact');
    while ($dao->fetch()) {
      $data = array();
      CRM_Core_DAO::storeValues($dao, $data);
      $triggerData = $this->getTriggerDataFromPost($op, $objectName, $objectId, $data);
      CRM_Civirules_Engine::triggerRule($this, clone $triggerData);
    }
  }
}