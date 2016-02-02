<?php

class CRM_Civirules_Utils_ObjectName {

  /**
   * Method to convert the object name to the entity for contacts
   *
   * @param string $objectName
   * @return string $entity
   * @access public
   * @static
   */
  public static function convertToEntity($objectName) {
    $entity = $objectName;
    switch($objectName) {
      case 'Individual':
      case 'Household':
      case 'Organization':
      case 'Profile':
        $entity = 'contact';
        break;
    }
    return $entity;
  }

}