<?php
/**
 * Class for CiviRules AgeComparison (extending generic ValueComparison)
 *
 * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
 * @license AGPL-3.0
 */

class CRM_CivirulesConditions_Contact_HasTag extends CRM_Civirules_Condition {

  private $conditionParams = array();

  /**
   * Method to set the Rule Condition data
   *
   * @param array $ruleCondition
   * @access public
   */
  public function setRuleConditionData($ruleCondition) {
    parent::setRuleConditionData($ruleCondition);
    $this->conditionParams = array();
    if (!empty($this->ruleCondition['condition_params'])) {
      $this->conditionParams = unserialize($this->ruleCondition['condition_params']);
    }
  }

  /**
   * This method returns true or false when an condition is valid or not
   *
   * @param CRM_Civirules_TriggerData_TriggerData $triggerData
   * @return bool
   * @access public
   * @abstract
   */
  public function isConditionValid(CRM_Civirules_TriggerData_TriggerData $triggerData) {
    $isConditionValid = false;
    $contact_id = $triggerData->getContactId();
    switch($this->conditionParams['operator']) {
      case 'in one of':
        $isConditionValid = $this->contactHasOneOfTags($contact_id, $this->conditionParams['tag_ids']);
        break;
      case 'in all of':
        $isConditionValid = $this->contactHasAllTags($contact_id, $this->conditionParams['tag_ids']);
        break;
      case 'not in':
        $isConditionValid = $this->contactHasNotTag($contact_id, $this->conditionParams['tag_ids']);
        break;
    }
    return $isConditionValid;
  }

  protected function contactHasNotTag($contact_id, $tag_ids) {
    $isValid = true;

    $tags = CRM_Core_BAO_EntityTag::getTag($contact_id);
    foreach($tag_ids as $tag_id) {
      if (in_array($tag_id, $tags)) {
        $isValid = false;
      }
    }

    return $isValid;
  }

  protected function contactHasAllTags($contact_id, $tag_ids) {
    $isValid = 0;

    $tags = CRM_Core_BAO_EntityTag::getTag($contact_id);
    foreach($tag_ids as $tag_id) {
      if (in_array($tag_id, $tags)) {
        $isValid++;
      }
    }

    if (count($tag_ids) == $isValid && count($tag_ids) > 0) {
      return true;
    }

    return false;
  }

  protected function contactHasOneOfTags($contact_id, $tag_ids) {
    $isValid = false;

    $tags = CRM_Core_BAO_EntityTag::getTag($contact_id);
    foreach($tag_ids as $tag_id) {
      if (in_array($tag_id, $tags)) {
        $isValid = true;
        break;
      }
    }

    return $isValid;
  }

  /**
   * Returns a redirect url to extra data input from the user after adding a condition
   *
   * Return false if you do not need extra data input
   *
   * @param int $ruleConditionId
   * @return bool|string
   * @access public
   * @abstract
   */
  public function getExtraDataInputUrl($ruleConditionId) {
    return CRM_Utils_System::url('civicrm/civirule/form/condition/contact_hastag/', 'rule_condition_id='.$ruleConditionId);
  }

  /**
   * Returns a user friendly text explaining the condition params
   * e.g. 'Older than 65'
   *
   * @return string
   * @access public
   */
  public function userFriendlyConditionParams() {
    $operators = CRM_CivirulesConditions_Contact_InGroup::getOperatorOptions();
    $operator = $this->conditionParams['operator'];
    $operatorLabel = ts('unknown');
    if (isset($operators[$operator])) {
      $operatorLabel = $operators[$operator];
    }

    $tags = '';
    foreach($this->conditionParams['tag_ids'] as $tid) {
      if (strlen($tags)) {
        $tags .= ', ';
      }
      $tags .= civicrm_api3('Tag', 'getvalue', array('return' => 'name', 'id' => $tid));
    }

    return $operatorLabel.' tags ('.$tags.')';
  }

  /**
   * Method to get operators
   *
   * @return array
   * @access protected
   */
  public static function getOperatorOptions() {
    return array(
      'in one of' => ts('In one of selected'),
      'in all of' => ts('In all selected'),
      'not in' => ts('Not in selected'),
    );
  }

}