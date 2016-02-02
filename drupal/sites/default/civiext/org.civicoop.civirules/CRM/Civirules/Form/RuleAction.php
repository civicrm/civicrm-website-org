<?php
/**
 * Form controller class to manage CiviRule/RuleAction
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 */
require_once 'CRM/Core/Form.php';

class CRM_Civirules_Form_RuleAction extends CRM_Core_Form {

  protected $ruleId = NULL;

  protected $ruleActionId;

  protected $ruleAction;

  protected $action;

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
    $this->ruleActionId = CRM_Utils_Request::retrieve('id', 'Integer');

    if ($this->ruleActionId) {
      $this->ruleAction = new CRM_Civirules_BAO_RuleAction();
      $this->ruleAction->id = $this->ruleActionId;
      if (!$this->ruleAction->find(true)) {
        throw new Exception('Civirules could not find ruleAction');
      }

      $this->action = new CRM_Civirules_BAO_Action();
      $this->action->id = $this->ruleAction->action_id;
      if (!$this->action->find(true)) {
        throw new Exception('Civirules could not find action');
      }

      $this->assign('action_label', $this->action->label);
    }

    $redirectUrl = CRM_Utils_System::url('civicrm/civirule/form/rule', 'action=update&id='.$this->ruleId, TRUE);
    $session = CRM_Core_Session::singleton();
    $session->pushUserContext($redirectUrl);
    if ($this->_action == CRM_Core_Action::DELETE) {
      $ruleActionId = CRM_Utils_Request::retrieve('id', 'Integer');
      CRM_Civirules_BAO_RuleAction::deleteWithId($ruleActionId);
      CRM_Utils_System::redirect($redirectUrl);
    }
  }

  /**
   * Function to perform post save processing (extends parent function)
   *
   * @access public
   */
  function postProcess() {
    $saveParams = array();
    $saveParams['rule_id'] = $this->_submitValues['rule_id'];
    $saveParams['delay'] = 'null';
    $saveParams['ignore_condition_with_delay'] = '0';
    if (!empty($this->_submitValues['rule_action_select'])) {
      if (!$this->ruleAction) {
        $this->ruleAction = new CRM_Civirules_BAO_RuleAction();
      }
      $this->ruleAction->action_id = $this->_submitValues['rule_action_select'];
      $saveParams['action_id'] = $this->_submitValues['rule_action_select'];
    }
    if ($this->ruleActionId) {
      $saveParams['id'] = $this->ruleActionId;
    }

    if (!empty($this->_submitValues['delay_select'])) {
      $delayClass = CRM_Civirules_Delay_Factory::getDelayClassByName($this->_submitValues['delay_select']);
      $delayClass->setValues($this->_submitValues);
      $saveParams['delay'] = serialize($delayClass);
      if (!empty($this->_submitValues['ignore_condition_with_delay'])) {
        $saveParams['ignore_condition_with_delay'] = '1';
      }
    }

    $ruleAction = CRM_Civirules_BAO_RuleAction::add($saveParams);

    $session = CRM_Core_Session::singleton();
    $session->setStatus('Action added to CiviRule '.CRM_Civirules_BAO_Rule::getRuleLabelWithId($this->_submitValues['rule_id']),
      'Action added', 'success');

    $action = CRM_Civirules_BAO_Action::getActionObjectById($this->ruleAction->action_id, true);
    $redirectUrl = $action->getExtraDataInputUrl($ruleAction['id']);
    if (empty($redirectUrl) || $this->ruleActionId) {
      $redirectUrl = CRM_Utils_System::url('civicrm/civirule/form/rule', 'action=update&id=' . $this->_submitValues['rule_id'], TRUE);
    } elseif (!$this->ruleActionId) {
      $redirectUrl .= '&action=add';
    }

    CRM_Utils_System::redirect($redirectUrl);
  }

  /**
   * Function to add the form elements
   *
   * @access protected
   */
  protected function createFormElements() {
    $this->add('hidden', 'rule_id');
    if ($this->ruleActionId) {
      $this->add('hidden', 'id');
    }
    $actionList = array(' - select - ') + CRM_Civirules_Utils::buildActionList();
    asort($actionList);
    $attributes = array();
    if (empty($this->ruleActionId)) {
      $this->add('select', 'rule_action_select', ts('Select Action'), $actionList, $attributes);
    }


    $delayList = array(' - No Delay - ') + CRM_Civirules_Delay_Factory::getOptionList();
    $this->add('select', 'delay_select', ts('Delay action to'), $delayList);
    foreach(CRM_Civirules_Delay_Factory::getAllDelayClasses() as $delay_class) {
      $delay_class->addElements($this);
    }
    $this->assign('delayClasses', CRM_Civirules_Delay_Factory::getAllDelayClasses());
    $this->add('checkbox', 'ignore_condition_with_delay', ts('Don\'t recheck condition upon processing of delayed action'));

    $this->addButtons(array(
      array('type' => 'next', 'name' => ts('Save'), 'isDefault' => TRUE,),
      array('type' => 'cancel', 'name' => ts('Cancel'))));
  }

  public function setDefaultValues() {
    $defaults['rule_id'] = $this->ruleId;

    foreach(CRM_Civirules_Delay_Factory::getAllDelayClasses() as $delay_class) {
      $delay_class->setDefaultValues($defaults);
    }

    if (!empty($this->ruleActionId)) {
      $defaults['rule_action_select'] = $this->ruleAction->action_id;
      $defaults['id'] = $this->ruleActionId;
      if (isset($this->ruleAction->ignore_condition_with_delay)) {
        $defaults['ignore_condition_with_delay'] = $this->ruleAction->ignore_condition_with_delay;
      }

      $delayClass = unserialize($this->ruleAction->delay);
      if ($delayClass) {
        $defaults['delay_select'] = get_class($delayClass);
        foreach($delayClass->getValues() as $key => $val) {
          $defaults[$key] = $val;
        }
      }

    }

    return $defaults;
  }

  /**
   * Function to set the form title based on action and data coming in
   *
   * @access protected
   */
  protected function setFormTitle() {
    $title = 'CiviRules Add Action';
    $this->assign('ruleActionHeader', 'Add Action to CiviRule '.CRM_Civirules_BAO_Rule::getRuleLabelWithId($this->ruleId));
    CRM_Utils_System::setTitle($title);
  }

  /**
   * Function to add validation action rules (overrides parent function)
   *
   * @access public
   */
  public function addRules() {
    if (empty($this->ruleActionId)) {
      $this->addFormRule(array(
        'CRM_Civirules_Form_RuleAction',
        'validateRuleAction'
      ));
    }
  }

  /**
   * Function to validate value of rule action form
   *
   * @param array $fields
   * @return array|bool
   * @access public
   * @static
   */
  static function validateRuleAction($fields) {
    $errors = array();
    if (isset($fields['rule_action_select']) && empty($fields['rule_action_select'])) {
      $errors['rule_action_select'] = ts('Action has to be selected, press CANCEL if you do not want to add an action');
    }
    if (!empty($fields['delay_select'])) {
      $delayClass = CRM_Civirules_Delay_Factory::getDelayClassByName($fields['delay_select']);
      $delayClass->validate($fields, $errors);
    }

    if (count($errors)) {
      return $errors;
    }

    return TRUE;
  }
}
