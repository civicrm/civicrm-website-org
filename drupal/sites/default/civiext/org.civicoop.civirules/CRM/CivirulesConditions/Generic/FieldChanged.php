<?php
/**
 * Abstract Class for CiviRules Generic Field Changed condition
 *
 * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
 * @license AGPL-3.0
 */

abstract class CRM_CivirulesConditions_Generic_FieldChanged extends CRM_Civirules_Condition {

  /**
   * Returns name of entity
   *
   * @return string
   * @access protected
   * @abstract
   */
  abstract protected function getEntity();

  /**
   * Returns name of the field
   *
   * @return string
   * @access protected
   * @abstract
   */
  abstract protected function getField();

  /**
   * Method to check if the condition is valid
   *
   * @param object CRM_Civirules_TriggerData_TriggerData $triggerData
   * @return bool
   * @access public
   */
  public function isConditionValid(CRM_Civirules_TriggerData_TriggerData $triggerData) {
    //not the right trigger. The trigger data should contain also
    if (!$triggerData instanceof CRM_Civirules_TriggerData_Interface_OriginalData) {
      return false;
    }

    $entity = $this->getEntity();
    if ($entity != $triggerData->getOriginalEntity()) {
      return false;
    }

    $fieldData = $this->getFieldData($triggerData);
    $originalData = $this->getOriginalFieldData($triggerData);

    if (empty($fieldData) && empty($originalData)) {
      return false; //both original and new data are null so assume not changed
    } elseif ($fieldData == $originalData) {
      return false; //both data are equal so assume not changed
    }

    return true;
  }

  /**
   * This method could be overridden in subclasses to
   * transform field data to a certain type
   *
   * E.g. a date field could be transformed to a DataTime object so that
   * the comparison is easier
   *
   * @param mixed $fieldData
   * @return mixed
   * @access protected
   */
  protected function transformFieldData($fieldData) {
    return $fieldData;
  }

  /**
   * Returns a redirect url to extra data input from the user after adding a condition
   *
   * Return false if you do not need extra data input
   *
   * @param int $ruleConditionId
   * @return bool|string
   * @access public
   */
  public function getExtraDataInputUrl($ruleConditionId) {
    return false;
  }

  /**
   * Method to get the field data
   *
   * @param object CRM_Civirules_TriggerData_TriggerData $triggerData
   * @return mixed|null
   * @access protected
   */
  protected function getFieldData(CRM_Civirules_TriggerData_TriggerData $triggerData) {
    $entity = $this->getEntity();
    $data = $triggerData->getEntityData($entity);
    $field = $this->getField();
    if (isset($data[$field])) {
      return $this->transformFieldData($data[$field]);
    }
    return null;
  }

  /**
   * Method to get the original field data
   *
   * @param object CRM_Civirules_TriggerData_Interface_OriginalData $triggerData
   * @return mixed|null
   * @access protected
   */
  protected function getOriginalFieldData(CRM_Civirules_TriggerData_Interface_OriginalData $triggerData) {
    $entity = $this->getEntity();
    if ($triggerData->getOriginalEntity() != $entity) {
      return null;
    }

    $data = $triggerData->getOriginalData();
    $field = $this->getField();
    if (isset($data[$field])) {
      return $this->transformFieldData($data[$field]);
    }
    return null;
  }

  /**
   * Returns an array with required entity names
   *
   * @return array
   * @access public
   */
  public function requiredEntities() {
    return array($this->getEntity());
  }
}