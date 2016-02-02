<?php

class CRM_Civirules_Utils_CustomDataFromPre {

  private static $customValues = array();

  public static function pre($op, $objectName, $objectId, $params) {
    if (!is_array($params)) {
      return;
    }
    if (isset($params['custom']) && is_array($params['custom'])) {
      foreach($params['custom'] as $fid => $custom_values) {
        foreach($custom_values as $id => $field) {
          $value = $field['value'];
          self::setCustomData($objectName, $fid, $value, $id);
        }
      }
    }
    foreach($params as $key => $value) {
      if (stripos($key, 'custom_')===0) {
        list($custom_, $fid, $id) = explode("_", $key, 3);
        self::setCustomData($objectName, $fid, $value, $id);
      }
    }
  }

  private static function setCustomData($objectName, $field_id, $value, $id) {
    $v = $value;

    /**
     * Convert value array from
     *   value_a => 1
     *   value_b => 1
     *
     * To
     *   [] => value_a
     *   [] => value_b
     *
     */
    if (CRM_Civirules_Utils_CustomField::isCustomFieldMultiselect($field_id) && is_array($value)) {
      $all_ones = true;
      foreach($value as $i => $j) {
        if ($j != 1) {
          $all_ones = false;
        }
      }
      if ($all_ones) {
        $v = array();
        foreach($value as $i => $j) {
          $v[] = $i;
        }
      }
    }
    self::$customValues[$field_id][$id] = $v;
  }

  public static function addCustomDataToTriggerData(CRM_Civirules_TriggerData_TriggerData $triggerData) {
    foreach(self::$customValues as $field_id => $values) {
      foreach($values as $id => $value) {
        $triggerData->setCustomFieldValue($field_id, $id, $value);
      }
    }
  }




}