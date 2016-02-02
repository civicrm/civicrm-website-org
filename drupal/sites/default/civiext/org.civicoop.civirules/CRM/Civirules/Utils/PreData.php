<?php

class CRM_Civirules_Utils_PreData {

  /**
   * Data set in pre and used for compare which field is changed
   *
   * @var array $preData
   */
  protected static $preData = array();

  /**
   * Method pre to store the entity data before the data in the database is changed
   * for the edit operation
   *
   * @param string $op
   * @param string $objectName
   * @param int $objectId
   * @param array $params
   * @access public
   * @static
   *
   */
  public static function pre($op, $objectName, $objectId, $params) {
    $nonPreEntities = array('GroupContact', 'EntityTag', 'ActionLog');
    if ($op != 'edit' || in_array($objectName, $nonPreEntities)) {
      return;
    }

    /**
     * Not every object in CiviCRM sets the object id in the pre hook
     * But we need this to fetch the current data state from the database.
     * So we check if the ID is in the params array and if so we use that id
     * for fetching the data
     *
     */
    $id = $objectId;
    if (empty($id) && isset($params['id']) && !empty($params['id'])) {
      $id = $params['id'];
    }

    if (empty($id)) {
      return;
    }

    //retrieve data as it is currently in the database
    $entity = CRM_Civirules_Utils_ObjectName::convertToEntity($objectName);
    if (!$entity) {
      return;
    }
    try {
      $data = civicrm_api3($entity, 'getsingle', array('id' => $id));
    } catch (Exception $e) {
      return;
    }
    self::setPreData($entity, $id, $data);
  }

  /**
   * Method to set the pre operation data
   *
   * @param string $entity
   * @param int $entityId
   * @param array $data
   * @access protected
   * @static
   */
  protected static function setPreData($entity, $entityId, $data) {
    self::$preData[$entity][$entityId] = $data;
  }

  /**
   * Method to get the pre operation data
   *
   * @param string $entity
   * @param int $entityId
   * @return array
   * @access protected
   * @static
   */
  public static function getPreData($entity, $entityId) {
    if (isset(self::$preData[$entity][$entityId])) {
      return self::$preData[$entity][$entityId];
    }
    return array();
  }

}
