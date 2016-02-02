<?php

class CRM_CivirulesActions_Form_Form extends CRM_Core_Form
{

  protected $ruleActionId = false;

  protected $ruleAction;

  protected $action;

  protected $rule;

  protected $trigger;

  /**
   * @var CRM_Civirules_Trigger
   */
  protected $triggerClass;

  /**
   * Overridden parent method to perform processing before form is build
   *
   * @access public
   */
  public function preProcess()
  {
    $this->ruleActionId = CRM_Utils_Request::retrieve('rule_action_id', 'Integer');

    $this->ruleAction = new CRM_Civirules_BAO_RuleAction();
    $this->ruleAction->id = $this->ruleActionId;

    $this->action = new CRM_Civirules_BAO_Action();
    $this->rule = new CRM_Civirules_BAO_Rule();
    $this->trigger = new CRM_Civirules_BAO_Trigger();

    if (!$this->ruleAction->find(true)) {
      throw new Exception('Civirules could not find ruleAction');
    }

    $this->action->id = $this->ruleAction->action_id;
    if (!$this->action->find(true)) {
      throw new Exception('Civirules could not find action');
    }

    $this->rule->id = $this->ruleAction->rule_id;
    if (!$this->rule->find(true)) {
      throw new Exception('Civirules could not find rule');
    }

    $this->trigger->id = $this->rule->trigger_id;
    if (!$this->trigger->find(true)) {
      throw new Exception('Civirules could not find trigger');
    }

    $this->triggerClass = CRM_Civirules_BAO_Trigger::getPostTriggerObjectByClassName($this->trigger->class_name, true);
    $this->triggerClass->setTriggerId($this->trigger->id);

    //set user context
    $session = CRM_Core_Session::singleton();
    $editUrl = CRM_Utils_System::url('civicrm/civirule/form/rule', 'action=update&id='.$this->rule->id, TRUE);
    $session->pushUserContext($editUrl);

    parent::preProcess();

    $this->setFormTitle();
  }

  function cancelAction() {
    if (isset($this->_submitValues['rule_action_id']) && $this->_action == CRM_Core_Action::ADD) {
      CRM_Civirules_BAO_RuleAction::deleteWithId($this->_submitValues['rule_action_id']);
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
    $defaultValues['rule_action_id'] = $this->ruleActionId;
    return $defaultValues;
  }

  public function postProcess() {
    $session = CRM_Core_Session::singleton();
    $session->setStatus('Action '.$this->action->label.' parameters updated to CiviRule '.$this->rule->label, 'Action parameters updated', 'success');

    $redirectUrl = CRM_Utils_System::url('civicrm/civirule/form/rule', 'action=update&id='.$this->rule->id, TRUE);
    CRM_Utils_System::redirect($redirectUrl);
  }

  /**
   * Method to set the form title
   *
   * @access protected
   */
  protected function setFormTitle() {
    $title = 'CiviRules Edit Action parameters';
    $this->assign('ruleActionHeader', 'Edit Action '.$this->action->label.' of CiviRule '.$this->rule->label);
    CRM_Utils_System::setTitle($title);
  }

}