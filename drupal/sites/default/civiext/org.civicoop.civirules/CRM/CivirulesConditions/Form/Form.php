<?php

class CRM_CivirulesConditions_Form_Form extends CRM_Core_Form
{

  protected $ruleConditionId = false;

  protected $ruleCondition;

  protected $condition;

  protected $rule;

  protected $trigger;

  /**
   * @var CRM_Civirules_Trigger
   */
  protected $triggerClass;

  /**
   * @var CRM_Civirules_Condition
   */
  protected $conditionClass;

  /**
   * Overridden parent method to perform processing before form is build
   *
   * @access public
   */
  public function preProcess()
  {
    $this->ruleConditionId = CRM_Utils_Request::retrieve('rule_condition_id', 'Integer');

    $this->ruleCondition = new CRM_Civirules_BAO_RuleCondition();
    $this->ruleCondition->id = $this->ruleConditionId;
    $ruleConditionData = array();
    CRM_Core_DAO::storeValues($this->ruleCondition, $ruleConditionData);

    $this->condition = new CRM_Civirules_BAO_Condition();
    $this->rule = new CRM_Civirules_BAO_Rule();
    $this->trigger = new CRM_Civirules_BAO_Trigger();

    if (!$this->ruleCondition->find(true)) {
      throw new Exception('Civirules could not find ruleCondition');
    }

    $this->condition->id = $this->ruleCondition->condition_id;
    if (!$this->condition->find(true)) {
      throw new Exception('Civirules could not find condition');
    }

    $this->rule->id = $this->ruleCondition->rule_id;
    if (!$this->rule->find(true)) {
      throw new Exception('Civirules could not find rule');
    }

    $this->trigger->id = $this->rule->trigger_id;
    if (!$this->trigger->find(true)) {
      throw new Exception('Civirules could not find trigger');
    }

    $this->conditionClass = CRM_Civirules_BAO_Condition::getConditionObjectById($this->condition->id, false);
    if ($this->conditionClass) {
      $this->conditionClass->setRuleConditionData($ruleConditionData);
    }

    $this->triggerClass = CRM_Civirules_BAO_Trigger::getTriggerObjectByTriggerId($this->trigger->id, true);
    $this->triggerClass->setTriggerId($this->trigger->id);
    $this->triggerClass->setTriggerParams($this->rule->trigger_params);

    parent::preProcess();

    $this->setFormTitle();

    //set user context
    $session = CRM_Core_Session::singleton();
    $editUrl = CRM_Utils_System::url('civicrm/civirule/form/rule', 'action=update&id='.$this->rule->id, TRUE);
    $session->pushUserContext($editUrl);
  }

  function cancelAction() {
    if (isset($this->_submitValues['rule_condition_id']) && $this->_action == CRM_Core_Action::ADD) {
      CRM_Civirules_BAO_RuleCondition::deleteWithId($this->_submitValues['rule_condition_id']);
    }
  }

  /**
   * Overridden parent method to set default values
   *
   * @return array $defaultValues
   * @access public
   */
  public function setDefaultValues() {
    $defaultValues = array();
    $defaultValues['rule_condition_id'] = $this->ruleConditionId;
    return $defaultValues;
  }

  public function postProcess() {
    $session = CRM_Core_Session::singleton();
    $session->setStatus('Condition '.$this->condition->label.' parameters updated to CiviRule '.$this->rule->label, 'Condition parameters updated', 'success');

    $redirectUrl = CRM_Utils_System::url('civicrm/civirule/form/rule', 'action=update&id='.$this->rule->id, TRUE);
    CRM_Utils_System::redirect($redirectUrl);
  }

  /**
   * Method to set the form title
   *
   * @access protected
   */
  protected function setFormTitle() {
    $title = 'CiviRules Edit Condition parameters';
    $this->assign('ruleConditionHeader', 'Edit Condition '.$this->condition->label.' of CiviRule '.$this->rule->label);
    CRM_Utils_System::setTitle($title);
  }

}