<?php
/**
 * Form controller class to manage CiviRule/RuleCondition
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 */
require_once 'CRM/Core/Form.php';

class CRM_Civirules_Form_RuleCondition extends CRM_Core_Form {

  protected $ruleId = NULL;

  /**
   * Function to buildQuickForm (extends parent function)
   *
   * @access public
   */
  function buildQuickForm() {
    $this->setFormTitle();
    $this->createFormElements();
    parent::buildQuickForm();
  }

  /**
   * Function to perform processing before displaying form (overrides parent function)
   *
   * @access public
   */
  function preProcess() {
    $this->ruleId = CRM_Utils_Request::retrieve('rid', 'Integer');
    $redirectUrl = CRM_Utils_System::url('civicrm/civirule/form/rule', 'action=update&id='.$this->ruleId, TRUE);
    $session = CRM_Core_Session::singleton();
    $session->pushUserContext($redirectUrl);
    $this->assign('countRuleConditions', CRM_Civirules_BAO_RuleCondition::countConditionsForRule($this->ruleId));
    if ($this->_action == CRM_Core_Action::DELETE) {
      $ruleConditionId = CRM_Utils_Request::retrieve('id', 'Integer');
      CRM_Civirules_BAO_RuleCondition::deleteWithId($ruleConditionId);
      CRM_Utils_System::redirect($redirectUrl);
    }
  }

  /**
   * Function to perform post save processing (extends parent function)
   *
   * @access public
   */
  function postProcess() {
    $session = CRM_Core_Session::singleton();
    $saveParams = array(
      'rule_id' => $this->_submitValues['rule_id'],
      'condition_id' => $this->_submitValues['rule_condition_select']
    );
    if (isset($this->_submitValues['rule_condition_link_select'])) {
      $saveParams['condition_link'] = $this->_submitValues['rule_condition_link_select'];
    }
    $ruleCondition = CRM_Civirules_BAO_RuleCondition::add($saveParams);

    $condition = CRM_Civirules_BAO_Condition::getConditionObjectById($ruleCondition['condition_id'], true);
    $redirectUrl = $condition->getExtraDataInputUrl($ruleCondition['id']);
    if (empty($redirectUrl)) {
      $redirectUrl = CRM_Utils_System::url('civicrm/civirule/form/rule', 'action=update&id=' . $this->_submitValues['rule_id'], TRUE);
    } else {
      $redirectUrl .= '&action=add';
    }

    $session->setStatus('Condition added to CiviRule '.CRM_Civirules_BAO_Rule::getRuleLabelWithId($this->_submitValues['rule_id']),
      'Condition added', 'success');
    CRM_Utils_System::redirect($redirectUrl);
  }

  /**
   * Function to add the form elements
   *
   * @access protected
   */
  protected function createFormElements() {
    $this->add('hidden', 'rule_id');
    $this->add('hidden', 'rid');
    $this->add('hidden', 'action');
    /*
     * add select list only if it is not the first condition
     */
    $linkList = array('AND' => 'AND', 'OR' =>'OR');
    $this->add('select', 'rule_condition_link_select', ts('Select Link Operator'), $linkList);
    $conditionList = array(' - select - ') + CRM_Civirules_Utils::buildConditionList();
    asort($conditionList);
    $this->add('select', 'rule_condition_select', ts('Select Condition'), $conditionList);

    $this->addButtons(array(
      array('type' => 'next', 'name' => ts('Save'), 'isDefault' => TRUE,),
      array('type' => 'cancel', 'name' => ts('Cancel'))));
  }

  public function setDefaultValues() {
    $defaults['rule_id'] = $this->ruleId;
    $defaults['rid'] = $this->ruleId;
    $defaults['action'] = 'add';
    return $defaults;
  }

  /**
   * Function to set the form title based on action and data coming in
   *
   * @access protected
   */
  protected function setFormTitle() {
    $title = 'CiviRules Add Condition';
    $this->assign('ruleConditionHeader', 'Add Condition to CiviRule '.CRM_Civirules_BAO_Rule::getRuleLabelWithId($this->ruleId));
    CRM_Utils_System::setTitle($title);
  }

  /**
   * Function to add validation condition rules (overrides parent function)
   *
   * @access public
   */
  public function addRules() {
    $this->addFormRule(array('CRM_Civirules_Form_RuleCondition', 'validateRuleCondition'));
    $this->addFormRule(array('CRM_Civirules_Form_RuleCondition', 'validateConditionEntities'));
  }

  /**
   * @param $fields
   */
  static function validateConditionEntities($fields) {
    $conditionClass = CRM_Civirules_BAO_Condition::getConditionObjectById($fields['rule_condition_select'], false);
    if (!$conditionClass) {
      $errors['rule_condition_select'] = ts('Not a valid condition, condition class is missing');
      return $errors;
    }
    $requiredEntities = $conditionClass->requiredEntities();
    $rule = new CRM_Civirules_BAO_Rule();
    $rule->id = $fields['rule_id'];
    $rule->find(true);
    $trigger = new CRM_Civirules_BAO_Trigger();
    $trigger->id = $rule->trigger_id;
    $trigger->find(true);

    $triggerObject = CRM_Civirules_BAO_Trigger::getPostTriggerObjectByClassName($trigger->class_name, true);
    $triggerObject->setTriggerId($trigger->id);
    $availableEntities = array();
    foreach($triggerObject->getProvidedEntities() as $entityDef) {
      $availableEntities[] = strtolower($entityDef->entity);
    }

    foreach($requiredEntities as $entity) {
      if (!in_array(strtolower($entity), $availableEntities)) {
        $errors['rule_condition_select'] = ts('This condition is not available with trigger %1', array(1 => $trigger->label));
        return $errors;
      }
    }
    return true;
  }

  /**
   * Function to validate value of rule condition form
   *
   * @param array $fields
   * @return array|bool
   * @access public
   * @static
   */
  static function validateRuleCondition($fields) {
    if (isset($fields['rule_condition_link_select']) && empty($fields['rule_condition_link_select'])) {
      $errors['rule_condition_link_select'] = ts('Link Operator can only be AND or OR');
      return $errors;
    }
    if (isset($fields['rule_condition_select']) && empty($fields['rule_condition_select'])) {
      $errors['rule_condition_select'] = ts('Condition has to be selected, press CANCEL if you do not want to add a condition');
      return $errors;
    }
    return TRUE;
  }
}
