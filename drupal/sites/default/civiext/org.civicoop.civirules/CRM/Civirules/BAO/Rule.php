<?php
/**
 * BAO Rule for CiviRule Rule
 * 
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 */
class CRM_Civirules_BAO_Rule extends CRM_Civirules_DAO_Rule {

  /**
   * Function to get values
   * 
   * @return array $result found rows with data
   * @access public
   * @static
   */
  public static function getValues($params) {
    $result = array();
    $rule = new CRM_Civirules_BAO_Rule();
    if (!empty($params)) {
      $fields = self::fields();
      foreach ($params as $key => $value) {
        if (isset($fields[$key])) {
          $rule->$key = $value;
        }
      }
    }
    $rule->find();
    while ($rule->fetch()) {
      $row = array();
      self::storeValues($rule, $row);
      $result[$row['id']] = $row;
    }
    return $result;
  }

  /**
   * Function to add or update rule
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
      throw new Exception('Params can not be empty when adding or updating a civirule rule');
    }

    if (!empty($params['id'])) {
      CRM_Utils_Hook::pre('edit', 'CiviRuleRule', $params['id'], $params);
    }
    else {
      CRM_Utils_Hook::pre('create', 'CiviRuleRule', NULL, $params);
    }

    $rule = new CRM_Civirules_BAO_Rule();
    $fields = self::fields();
    foreach ($params as $key => $value) {
      if (isset($fields[$key])) {
        $rule->$key = $value;
      }
    }
    if (!isset($rule->name) || empty($rule->name)) {
      if (isset($rule->label)) {
        $rule->name = CRM_Civirules_Utils::buildNameFromLabel($rule->label);
      }
    }
    $rule->save();
    self::storeValues($rule, $result);

    if (!empty($params['id'])) {
      CRM_Utils_Hook::post('edit', 'CiviRuleRule', $rule->id, $rule);
    }
    else {
      CRM_Utils_Hook::post('create', 'CiviRuleRule', $rule->id, $rule);
    }

    return $result;
  }

  /**
   * Function to delete a rule with id
   * 
   * @param int $ruleId
   * @throws Exception when ruleId is empty
   * @access public
   * @static
   */
  public static function deleteWithId($ruleId) {
    if (empty($ruleId)) {
      throw new Exception('rule id can not be empty when attempting to delete a civirule rule');
    }

    CRM_Utils_Hook::pre('delete', 'CiviRuleRule', $ruleId, CRM_Core_DAO::$_nullArray);

    CRM_Civirules_BAO_RuleAction::deleteWithRuleId($ruleId);
    CRM_Civirules_BAO_RuleCondition::deleteWithRuleId($ruleId);
    $rule = new CRM_Civirules_BAO_Rule();
    $rule->id = $ruleId;
    $rule->delete();

    CRM_Utils_Hook::post('delete', 'CiviRuleRule', $ruleId, CRM_Core_DAO::$_nullArray);

    return;
  }

  /**
   * Function to disable a rule
   * 
   * @param int $ruleId
   * @throws Exception when ruleId is empty
   * @access public
   * @static
   */
  public static function disable($ruleId) {
    if (empty($ruleId)) {
      throw new Exception('rule id can not be empty when attempting to disable a civirule rule');
    }
    $rule = new CRM_Civirules_BAO_Rule();
    $rule->id = $ruleId;
    $rule->find(true);
    self::add(array('id' => $rule->id, 'is_active' => 0));
  }

  /**
   * Function to enable an rule
   * 
   * @param int $ruleId
   * @throws Exception when ruleId is empty
   * @access public
   * @static
   */
  public static function enable($ruleId) {
    if (empty($ruleId)) {
      throw new Exception('rule id can not be empty when attempting to enable a civirule rule');
    }
    $rule = new CRM_Civirules_BAO_Rule();
    $rule->id = $ruleId;
    $rule->find(true);
    self::add(array('id' => $rule->id, 'is_active' => 1));
  }

  /**
   * Function to retrieve the label of a rule with ruleId
   * 
   * @param int $ruleId
   * @return string $rule->label
   * @access public
   * @static
   */
  public static function getRuleLabelWithId($ruleId) {
    if (empty($ruleId)) {
      return '';
    }
    $rule = new CRM_Civirules_BAO_Rule();
    $rule->id = $ruleId;
    $rule->find(true);
    return $rule->label;
  }

  /**
   * Function to check if a label already exists in the rule table
   *
   * @param $labelToBeChecked
   * @return bool
   * @access public
   * @static
   */
  public static function labelExists($labelToBeChecked) {
    $rule = new CRM_Civirules_BAO_Rule();
    $rule->label = $labelToBeChecked;
    if ($rule->count() > 0) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Returns an array with rules which should be triggered immediately
   *
   * @param string $objectName ObjectName in the Post hook
   * @param string $op op in the Post hook
   * @return array
   */
  public static function findRulesByObjectNameAndOp($objectName, $op)
  {
    $triggers = array();
    $sql = "SELECT r.id AS rule_id, t.id AS trigger_id, t.class_name, r.trigger_params
            FROM `civirule_rule` r
            INNER JOIN `civirule_trigger` t ON r.trigger_id = t.id AND t.is_active = 1
            WHERE r.`is_active` = 1 AND t.cron = 0 AND t.object_name = %1 AND t.op = %2";
    $params[1] = array($objectName, 'String');
    $params[2] = array($op, 'String');

    $dao = CRM_Core_DAO::executeQuery($sql, $params);
    while ($dao->fetch()) {
      $triggerObject = CRM_Civirules_BAO_Trigger::getPostTriggerObjectByClassName($dao->class_name, false);
      if ($triggerObject !== false) {
        $triggerObject->setTriggerId($dao->trigger_id);
        $triggerObject->setRuleId($dao->rule_id);
        $triggerObject->setTriggerParams($dao->trigger_params);
        $triggers[] = $triggerObject;
      }
    }
    return $triggers;
  }

  /**
   * Returns an array with cron triggers which should be triggered in the cron
   *
   * @return array
   */
  public static function findRulesForCron()
  {
    $cronTriggers = array();
    $sql = "SELECT r.id AS rule_id, t.id AS trigger_id, t.class_name, r.trigger_params
            FROM `civirule_rule` r
            INNER JOIN `civirule_trigger` t ON r.trigger_id = t.id AND t.is_active = 1
            WHERE r.`is_active` = 1 AND t.cron = 1";

    $dao = CRM_Core_DAO::executeQuery($sql);
    while ($dao->fetch()) {
      $cronTriggerObject = CRM_Civirules_BAO_Trigger::getTriggerObjectByClassName($dao->class_name, false);
      if ($cronTriggerObject !== false) {
        $cronTriggerObject->setTriggerId($dao->trigger_id);
        $cronTriggerObject->setRuleId($dao->rule_id);
        $cronTriggerObject->setTriggerParams($dao->trigger_params);
        $cronTriggers[] = $cronTriggerObject;
      }
    }
    return $cronTriggers;
  }

  /*
   * Function to get latest rule id
   *
   * @return int $ruleId
   * @access public
   * @static
   */
  public static function getLatestRuleId() {
    $rule = new CRM_Civirules_BAO_Rule();
    $query = 'SELECT MAX(id) AS maxId FROM '.$rule->tableName();
    $dao = CRM_Core_DAO::executeQuery($query);
    if ($dao->fetch()) {
      return $dao->maxId;
    }
  }
}