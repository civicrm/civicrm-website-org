<?php
/**
 * BAO RuleCondition for CiviRule Rule Condition
 * 
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 */
class CRM_Civirules_BAO_RuleCondition extends CRM_Civirules_DAO_RuleCondition {

  /**
   * Function to get values
   * 
   * @return array $result found rows with data
   * @access public
   * @static
   */
  public static function getValues($params) {
    $result = array();
    $ruleCondition = new CRM_Civirules_BAO_RuleCondition();
    if (!empty($params)) {
      $fields = self::fields();
      foreach ($params as $key => $value) {
        if (isset($fields[$key])) {
          $ruleCondition->$key = $value;
        }
      }
    }
    $ruleCondition->find();
    while ($ruleCondition->fetch()) {
      $row = array();
      self::storeValues($ruleCondition, $row);
      if (!empty($row['condition_id'])) {
        $result[$row['id']] = $row;
      } else {
        //invalid ruleCondition because no there is no linked condition
        CRM_Civirules_BAO_RuleCondition::deleteWithId($row['id']);
      }
    }
    return $result;
  }

  /**
   * Function to add or update rule condition
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
      throw new Exception('Params can not be empty when adding or updating a civirule rule condition');
    }
    $ruleCondition = new CRM_Civirules_BAO_RuleCondition();
    $fields = self::fields();
    foreach ($params as $key => $value) {
      if (isset($fields[$key])) {
        $ruleCondition->$key = $value;
      }
    }
    $ruleCondition->save();
    self::storeValues($ruleCondition, $result);
    return $result;
  }

  /**
   * Function to delete a rule condition with id
   * 
   * @param int $ruleConditionId
   * @throws Exception when ruleConditionId is empty
   * @access public
   * @static
   */
  public static function deleteWithId($ruleConditionId) {
    if (empty($ruleConditionId)) {
      throw new Exception('rule condition id can not be empty when attempting to delete a civirule rule condition');
    }
    $ruleCondition = new CRM_Civirules_BAO_RuleCondition();
    $ruleCondition->id = $ruleConditionId;
    if ($ruleCondition->find(true)) {
      $ruleCondition->delete();
      CRM_Civirules_BAO_RuleCondition::emptyConditionLinkForFirstCondition($ruleCondition->rule_id);
    }
    return;
  }

  public static function emptyConditionLinkForFirstCondition($rule_id) {
    $conditionParams = array(
        'is_active' => 1,
        'rule_id' => $rule_id
    );
    $ruleConditions = CRM_Civirules_BAO_RuleCondition::getValues($conditionParams);
    if (count($ruleConditions)) {
      $ruleCondition = reset($ruleConditions);
      $ruleCondition['condition_link'] = 'null';
      CRM_Civirules_BAO_RuleCondition::add($ruleCondition);
    }
  }

  /**
   * Function to disable a rule condition
   * 
   * @param int $ruleConditionId
   * @throws Exception when ruleConditionId is empty
   * @access public
   * @static
   */
  public static function disable($ruleConditionId) {
    if (empty($ruleConditionId)) {
      throw new Exception('rule condition id can not be empty when attempting to disable a civirule rule condition');
    }
    $ruleCondition = new CRM_Civirules_BAO_RuleCondition();
    $ruleCondition->id = $ruleConditionId;
    $ruleCondition->find(true);
    self::add(array('id' => $ruleCondition->id, 'is_active' => 0));
  }

  /**
   * Function to enable a rule condition
   * 
   * @param int $ruleConditionId
   * @throws Exception when ruleConditionId is empty
   * @access public
   * @static
   */
  public static function enable($ruleConditionId) {
    if (empty($ruleConditionId)) {
      throw new Exception('rule condition id can not be empty when attempting to enable a civirule rule condition');
    }
    $ruleCondition = new CRM_Civirules_BAO_RuleCondition();
    $ruleCondition->id = $ruleConditionId;
    $ruleCondition->find(true);
    self::add(array('id' => $ruleCondition->id, 'is_active' => 1));
  }

  /**
   * Function to delete all rule conditions with rule id
   *
   * @param int $ruleId
   * @access public
   * @static
   */
  public static function deleteWithRuleId($ruleId) {
    $ruleCondition = new CRM_Civirules_BAO_RuleCondition();
    $ruleCondition->rule_id = $ruleId;
    $ruleCondition->find(FALSE);
    while ($ruleCondition->fetch()) {
      $ruleCondition->delete();
    }
  }

  /**
   * Function to count the number of conditions for a rule
   *
   * @param int $ruleId
   * @return int
   * @access public
   * @static
   */
  public static function countConditionsForRule($ruleId) {
    $ruleCondition = new CRM_Civirules_BAO_RuleCondition();
    $ruleCondition->rule_id = $ruleId;
    return $ruleCondition->count();
  }
}