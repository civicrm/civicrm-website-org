<?php
/**
 * DAO RuleAction for table civirule_rule_action
 * 
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 */
class CRM_Civirules_DAO_RuleAction extends CRM_Core_DAO {
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
    return 'civirule_rule_action';
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
        'rule_id' => array(
          'name' => 'rule_id',
          'type' => CRM_Utils_Type::T_INT
        ),
        'action_id' => array(
          'name' => 'action_id',
          'type' => CRM_Utils_Type::T_INT
        ),
        'action_params' => array(
          'name' => 'action_params',
          'type' => CRM_Utils_Type::T_TEXT
        ),
        'delay' => array(
          'name' => 'delay',
          'type' => CRM_Utils_Type::T_TEXT
        ),
        'ignore_condition_with_delay' => array(
          'name' => 'ignore_condition_with_delay',
          'type' => CRM_Utils_Type::T_INT,
        ),
        'is_active' => array(
          'name' => 'is_active',
          'type' => CRM_Utils_Type::T_INT,
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
        'rule_id' => 'rule_id',
        'action_id' => 'action_id',
        'action_params' => 'action_params',
        'delay' => 'delay',
        'ignore_condition_with_delay' => 'ignore_condition_with_delay',
        'is_active' => 'is_active'
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