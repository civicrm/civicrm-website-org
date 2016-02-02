<?php
/**
 * Class for CiviRules post trigger handling
 *
 * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
 * @license AGPL-3.0
 */

class CRM_Civirules_Trigger_Post extends CRM_Civirules_Trigger {

  protected $objectName;

  protected $op;

  public function setTriggerId($triggerId) {
    parent::setTriggerId($triggerId);

    $trigger = new CRM_Civirules_BAO_Trigger();
    $trigger->id = $this->triggerId;
    if (!$trigger->find(true)) {
      throw new Exception('Civirules: could not find trigger with ID: '.$this->triggerId);
    }
    $this->objectName = $trigger->object_name;
    $this->op = $trigger->op;
  }

  /**
   * Returns an array of entities on which the trigger reacts
   *
   * @return CRM_Civirules_TriggerData_EntityDefinition
   */
  protected function reactOnEntity() {
    $entity = CRM_Civirules_Utils_ObjectName::convertToEntity($this->objectName);
    return new CRM_Civirules_TriggerData_EntityDefinition($this->objectName, $entity, $this->getDaoClassName(), $entity);
  }

  /**
   * Return the name of the DAO Class. If a dao class does not exist return an empty value
   *
   * @return string
   */
  protected function getDaoClassName() {
    $daoClassName = CRM_Core_DAO_AllCoreTables::getFullName($this->objectName);
    return $daoClassName;
  }

  /**
   * Method post
   *
   * @param string $op
   * @param string $objectName
   * @param int $objectId
   * @param object $objectRef
   * @access public
   * @static
   */
  public static function post( $op, $objectName, $objectId, &$objectRef ) {
    $extensionConfig = CRM_Civirules_Config::singleton();
    if (!in_array($op,$extensionConfig->getValidTriggerOperations())) {
      return;
    }
    //find matching rules for this objectName and op
    $triggers = CRM_Civirules_BAO_Rule::findRulesByObjectNameAndOp($objectName, $op);
    foreach($triggers as $trigger) {
      if ($trigger instanceof CRM_Civirules_Trigger_Post) {
        $trigger->triggerTrigger($op, $objectName, $objectId, $objectRef);
      }
    }
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
    CRM_Civirules_Engine::triggerRule($this, clone $triggerData);
  }

  /**
   * Get trigger data belonging to this specific post event
   *
   * Sub classes could override this method. E.g. a post on GroupContact doesn't give on object of GroupContact
   * it rather gives an array with contact Id's
   *
   * @param $op
   * @param $objectName
   * @param $objectId
   * @param $objectRef
   * @return CRM_Civirules_TriggerData_Edit|CRM_Civirules_TriggerData_Post
   */
  protected function getTriggerDataFromPost($op, $objectName, $objectId, $objectRef) {
    $entity = CRM_Civirules_Utils_ObjectName::convertToEntity($objectName);
    $data = $this->convertObjectRefToDataArray($entity, $objectRef, $objectId);

    if ($op == 'edit') {
      //set also original data with an edit event
      $oldData = CRM_Civirules_Utils_PreData::getPreData($entity, $objectId);
      $triggerData = new CRM_Civirules_TriggerData_Edit($entity, $objectId, $data, $oldData);
    } else {
      $triggerData = new CRM_Civirules_TriggerData_Post($entity, $objectId, $data);
    }

    $this->alterTriggerData($triggerData);

    return $triggerData;
  }

  protected function convertObjectRefToDataArray($entity, $objectRef, $id) {
    //set data
    $data = array();
    if (is_object($objectRef)) {
      CRM_Core_DAO::storeValues($objectRef, $data);
    } elseif (is_array($objectRef)) {
      $data = $objectRef;
    }

    return $data;
  }


}