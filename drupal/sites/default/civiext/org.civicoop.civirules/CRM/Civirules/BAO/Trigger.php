<?php
/**
 * BAO Trigger for CiviRule Trigger
 * 
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 */
class CRM_Civirules_BAO_Trigger extends CRM_Civirules_DAO_Trigger {

  /**
   * Function to get values
   * 
   * @return array $result found rows with data
   * @access public
   * @static
   */
  public static function getValues($params) {
    $result = array();
    $trigger = new CRM_Civirules_BAO_Trigger();
    if (!empty($params)) {
      $fields = self::fields();
      foreach ($params as $key => $value) {
        if (isset($fields[$key])) {
          $trigger->$key = $value;
        }
      }
    }
    $trigger->find();
    while ($trigger->fetch()) {
      $row = array();
      self::storeValues($trigger, $row);
      $result[$row['id']] = $row;
    }
    return $result;
  }

  /**
   * Function to add or update trigger
   * 
   * @param array $params 
   * @return array $result
   * @access public
   * @throws Exception when params is empty
   * @static
   */
  public static function add($params) {
    $result = array();
    if (empty($params)) {
      throw new Exception('Params can not be empty when adding or updating a civirule trigger');
    }
    $trigger = new CRM_Civirules_BAO_Trigger();
    $fields = self::fields();
    foreach ($params as $key => $value) {
      if (isset($fields[$key])) {
        $trigger->$key = $value;
      }
    }
    if (!isset($trigger->name) || empty($trigger->name)) {
      $trigger->name = CRM_Civirules_Utils::buildNameFromLabel($trigger->label);
    }
    $trigger->save();
    self::storeValues($trigger, $result);
    return $result;
  }

  /**
   * Function to delete a trigger with id
   * 
   * @param int $triggerId
   * @throws Exception when triggerId is empty
   * @access public
   * @static
   */
  public static function deleteWithId($triggerId) {
    if (empty($triggerId)) {
      throw new Exception('trigger id can not be empty when attempting to delete a civirule trigger');
    }
    $trigger = new CRM_Civirules_BAO_Trigger();
    $trigger->id = $trigger;
    $trigger->delete();
    return;
  }

  /**
   * Function to disable a trigger
   * 
   * @param int $triggerId
   * @throws Exception when triggerId is empty
   * @access public
   * @static
   */
  public static function disable($triggerId) {
    if (empty($triggerId)) {
      throw new Exception('trigger id can not be empty when attempting to disable a civirule trigger');
    }
    $trigger = new CRM_Civirules_BAO_Trigger();
    $trigger->id = $triggerId;
    $trigger->find(true);
    self::add(array('id' => $trigger->id, 'is_active' => 0));
  }

  /**
   * Function to enable a trigger
   * 
   * @param int $triggerId
   * @throws Exception when triggerId is empty
   * @access public
   * @static
   */
  public static function enable($triggerId) {
    if (empty($triggerId)) {
      throw new Exception('trigger id can not be empty when attempting to enable a civirule trigger');
    }
    $trigger = new CRM_Civirules_BAO_Trigger();
    $trigger->id = $triggerId;
    $trigger->find(true);
    self::add(array('id' => $trigger->id, 'is_active' => 1));
  }

  /**
   * Function to retrieve the label of an eva triggerent with triggerId
   * 
   * @param int $triggerId
   * @return string $trigger->label
   * @access public
   * @static
   */
  public static function getTriggerLabelWithId($triggerId) {
    if (empty($triggerId)) {
      return '';
    }
    $trigger = new CRM_Civirules_BAO_Trigger();
    $trigger->id = $triggerId;
    $trigger->find(true);
    return $trigger->label;
  }

  /**
   * Get the trigger class based on class name or on objectName
   *
   * @param $className
   * @param bool $abort
   * @return CRM_Civirules_Trigger
   * @throws Exception if abort is set to true and class does not exist or is not valid
   */
  public static function getPostTriggerObjectByClassName($className, $abort=true) {
    if (empty($className)) {
      $className = 'CRM_Civirules_Trigger_Post';
    }
    return self::getTriggerObjectByClassName($className, $abort);
  }

  /**
   * Get the trigger class for this trigger
   *
   * @param $className
   * @param bool $abort if true this function will throw an exception if class could not be instantiated
   * @return CRM_Civirules_Trigger
   * @throws Exception if abort is set to true and class does not exist or is not valid
   */
  public static function getTriggerObjectByClassName($className, $abort=true)
  {
    if (!class_exists($className)) {
      if ($abort) {

        throw new Exception('CiviRule trigger class "' . $className . '" does not exist');
      }
      return false;
    }

    $object = new $className();
    if (!$object instanceof CRM_Civirules_Trigger) {
      if ($abort) {
        throw new Exception('CiviRule trigger class "' . $className . '" is not a subclass of CRM_Civirules_Trigger');
      }
      return false;
    }
    return $object;
  }

  public static function getTriggerObjectByTriggerId($triggerId, $abort=true) {
    $sql = "SELECT t.*
            FROM `civirule_trigger` t
            WHERE t.`is_active` = 1 AND t.id = %1";

    $params[1] = array($triggerId, 'Integer');
    $dao = CRM_Core_DAO::executeQuery($sql, $params);
    if ($dao->fetch()) {
      if (!empty($dao->object_name) && !empty($dao->op) && empty($dao->cron)) {
        return self::getPostTriggerObjectByClassName($dao->class_name, $abort);
      } elseif (!empty($dao->class_name)) {
        return self::getTriggerObjectByClassName($dao->class_name, $abort);
      }
    }

    if ($abort) {
      throw new Exception('Could not find trigger with ID: '.$triggerId);
    }
  }

  /**
   * Method to check if a trigger exists with class_name or object_name/op
   *
   * @param array $params
   * @return bool
   * @access public
   * @static
   */
  public static function triggerExists($params) {
    if (isset($params['class_name']) && !empty($params['class_name'])) {
      $checkParams['class_name'] = $params['class_name'];
    } else {
      if (isset($params['object_name']) && isset($params['op']) && !empty($params['object_name']) && !empty($params['op'])) {
        $checkParams['object_name'] = $params['object_name'];
        $checkParams['op'] = $params['op'];
      }
    }
    if (!empty($checkParams)) {
      $foundTriggers = self::getValues($checkParams);
      if (!empty($foundTriggers)) {
        return TRUE;
      }
    }
    return FALSE;
  }
}