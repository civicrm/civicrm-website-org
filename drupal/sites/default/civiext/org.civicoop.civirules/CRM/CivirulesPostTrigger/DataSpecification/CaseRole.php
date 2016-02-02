<?php

/**
 * Helper class to specify structure of CaseRoles. Which consists of a case_id, contact_id and a role
 * Class CRM_CivirulesPostTrigger_DAO_CaseRole
 */
class CRM_CivirulesPostTrigger_DataSpecification_CaseRole {

  /**
   * static instance to hold the field values
   *
   * @var array
   */
  static $_fields = null;
  /**
   * static instance to hold the keys used in $_fields for each field.
   *
   * @var array
   */
  static $_fieldKeys = null;

  /**
   * Returns all the column names of this table
   *
   * @return array
   */
  static function &fields()
  {
    if (!(self::$_fields)) {
      self::$_fields = array(
        'contact_id' => array(
          'name' => 'contact_id',
          'type' => CRM_Utils_Type::T_INT,
          'description' => 'Contact ID of contact record given case belongs to.',
          'required' => true,
          'FKClassName' => 'CRM_Contact_DAO_Contact',
        ),
        'is_client' => array(
          'name' => 'is_client',
          'type' => CRM_Utils_Type::T_BOOLEAN,
        ),
      );
    }
    return self::$_fields;
  }

  /**
   * Returns an array containing, for each field, the arary key used for that
   * field in self::$_fields.
   *
   * @return array
   */
  static function &fieldKeys()
  {
    if (!(self::$_fieldKeys)) {
      self::$_fieldKeys = array(
        'contact_id' => 'contact_id',
        'is_client' => 'is_client',
      );
    }
    return self::$_fieldKeys;
  }

}