<?php
/**
 * DAO Trigger for table civirule_trigger
 * 
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 */
class CRM_Civirules_DAO_Trigger extends CRM_Core_DAO {
  /**
   * static instance to hold the field values
   *
   * @var array
   * @static
   */
  static $_fields = null;
  static $_export = null;
  /**
   * empty definition for virtual function
   */
  static function getTableName() {
    return 'civirule_trigger';
  }
  /**
   * returns all the column names of this table
   *
   * @access public
   * @return array
   */
  static function &fields() {
    if (!(self::$_fields)) {
      self::$_fields = array(
        'id' => array(
          'name' => 'id',
          'type' => CRM_Utils_Type::T_INT,
          'required' => true
        ) ,
        'name' => array(
          'name' => 'name',
          'type' => CRM_Utils_Type::T_STRING,
          'maxlength' => 80,
        ),
        'label' => array(
          'name' => 'label',
          'type' => CRM_Utils_Type::T_STRING,
          'maxlength' => 128,
        ),
        'object_name' => array(
          'name' => 'object_name',
          'type' => CRM_Utils_Type::T_STRING,
          'maxlength' => 45,
        ),
        'op' => array(
          'name' => 'op',
          'type' => CRM_Utils_Type::T_STRING,
          'maxlength' => 45
        ),
        'cron' => array(
          'name' => 'cron',
          'type' => CRM_Utils_Type::T_INT
        ),
        'class_name' => array(
          'name' => 'class_name',
          'type' => CRM_Utils_Type::T_STRING,
          'maxlength' => 128
        ),
        'is_active' => array(
          'name' => 'is_active',
          'type' => CRM_Utils_Type::T_INT,
        ),
        'created_date' => array(
          'name' => 'created_date',
          'type' => CRM_Utils_Type::T_DATE
        ),
        'created_user_id' => array(
          'name' => 'created_user_id',
          'type' => CRM_Utils_Type::T_INT
        ),
        'modified_date' => array(
          'name' => 'modified_date',
          'type' => CRM_Utils_Type::T_DATE
        ),
        'modified_user_id' => array(
          'name' => 'modified_user_id',
          'type' => CRM_Utils_Type::T_INT
        ),
      );
    }
    return self::$_fields;
  }
  /**
   * Returns an array containing, for each field, the array key used for that
   * field in self::$_fields.
   *
   * @access public
   * @return array
   */
  static function &fieldKeys() {
    if (!(self::$_fieldKeys)) {
      self::$_fieldKeys = array(
        'id' => 'id',
        'name' => 'name',
        'label' => 'label',
        'object_name' => 'object_name',
        'op' => 'op',
        'cron' => 'cron',
        'class_name' => 'class_name',
        'is_active' => 'is_active',
        'created_date' => 'created_date',
        'created_user_id' => 'created_user_id',
        'modified_date' => 'modified_date',
        'modified_user_id' => 'modified_user_id',
      );
    }
    return self::$_fieldKeys;
  }
  /**
   * returns the list of fields that can be exported
   *
   * @access public
   * return array
   * @static
   */
  static function &export($prefix = false)
  {
    if (!(self::$_export)) {
      self::$_export = array();
      $fields = self::fields();
      foreach($fields as $name => $field) {
        if (CRM_Utils_Array::value('export', $field)) {
          if ($prefix) {
            self::$_export['activity'] = & $fields[$name];
          } else {
            self::$_export[$name] = & $fields[$name];
          }
        }
      }
    }
    return self::$_export;
  }
}