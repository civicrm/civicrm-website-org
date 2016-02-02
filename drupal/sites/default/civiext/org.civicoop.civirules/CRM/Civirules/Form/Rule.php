<?php
/**
 * Form controller class to manage CiviRule/Rule
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 * 
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 */
require_once 'CRM/Core/Form.php';

class CRM_Civirules_Form_Rule extends CRM_Core_Form {
  
  protected $ruleId = NULL;

  protected $rule;

  protected $trigger;

  protected $postRuleBlock = '';

  /**
   * @var CRM_Civirules_Trigger
   */
  protected $triggerClass;

  /**
   * Function to buildQuickForm (extends parent function)
   * 
   * @access public
   */
  function buildQuickForm() {
    $this->setFormTitle();
    $this->createFormElements();
    $this->assign('postRuleBlock', $this->postRuleBlock);
    parent::buildQuickForm();
  }

  /**
   * Post rule details are shown on the form just between the rule name and the
   * linked trigger
   *
   * @return string
   */
  public function getPostRuleBlock() {
    return $this->postRuleBlock;
  }

  /**
   * Post rule details are shown on the form just between the rule name and the
   * linked trigger
   *
   * @param $postRuleDetails
   */
  public function setPostRuleBlock($postRuleBlock) {
    $this->postRuleBlock = $postRuleBlock;
    $this->assign('postRuleBlock', $this->postRuleBlock);
  }

  /**
   * Function to perform processing before displaying form (overrides parent function)
   * 
   * @access public
   */
  function preProcess() {
    $this->ruleId = CRM_Utils_Request::retrieve('id', 'Integer');

    $this->rule = new CRM_Civirules_BAO_Rule();
    $this->trigger = new CRM_Civirules_BAO_Trigger();

    $this->assign('trigger_edit_params', false);
    $this->triggerClass = false;
    if (!empty($this->ruleId)) {
      $this->rule->id = $this->ruleId;
      if (!$this->rule->find(TRUE)) {
        throw new Exception('Civirules could not find rule');
      }

      $this->trigger->id = $this->rule->trigger_id;
      if (!$this->trigger->find(TRUE)) {
        throw new Exception('Civirules could not find trigger');
      }

      $this->triggerClass = CRM_Civirules_BAO_Trigger::getTriggerObjectByTriggerId($this->trigger->id, TRUE);
      $this->triggerClass->setTriggerId($this->trigger->id);
      $this->triggerClass->setRuleId($this->rule->id);
      $this->triggerClass->setTriggerParams($this->rule->trigger_params);

      $this->assign('trigger_edit_params', $this->triggerClass->getExtraDataInputUrl($this->ruleId));
    }
    $this->assign('triggerClass', $this->triggerClass);

    $ruleConditionAddUrl = CRM_Utils_System::url('civicrm/civirule/form/rule_condition', 'reset=1&action=add&rid='.$this->ruleId, TRUE);
    $ruleActionAddUrl = CRM_Utils_System::url('civicrm/civirule/form/rule_action', 'reset=1&action=add&rid='.$this->ruleId, TRUE);
    $this->assign('ruleConditionAddUrl', $ruleConditionAddUrl);
    $this->assign('ruleActionAddUrl', $ruleActionAddUrl);

    $this->assign('action', $this->_action);
    $this->assign('rule', $this->rule);
    $session = CRM_Core_Session::singleton();
    switch($this->_action) {
      case CRM_Core_Action::DISABLE:
        CRM_Civirules_BAO_Rule::disable($this->ruleId);
        $session->setStatus('CiviRule disabled', 'Disable', 'success');
        CRM_Utils_System::redirect($session->readUserContext());
        break;
      case CRM_Core_Action::ENABLE:
        CRM_Civirules_BAO_Rule::enable($this->ruleId);
        $session->setStatus('CiviRule enabled', 'Enable', 'success');
        CRM_Utils_System::redirect($session->readUserContext());
        break;
    }
  }

  /**
   * Function to perform post save processing (extends parent function)
   * 
   * @access public
   */
  function postProcess() {
    $session = CRM_Core_Session::singleton();
    $userId = $session->get('userID');
    if ($this->_action == CRM_Core_Action::DELETE) {
      CRM_Civirules_BAO_Rule::deleteWithId($this->ruleId);
      $session->setStatus('CiviRule deleted', 'Delete', 'success');
      CRM_Utils_System::redirect($session->readUserContext());
    }
    
    $this->saveRule($this->_submitValues, $userId);
    $this->saveRuleTrigger($this->_submitValues);
    $session->setStatus('Rule with linked Trigger saved succesfully', 'CiviRule saved', 'success');
    /*
     * if add mode, set user context to form in edit mode to add conditions and actions
     */
    if ($this->_action == CRM_Core_Action::ADD || $this->_action == CRM_Core_Action::UPDATE) {
      $editUrl = CRM_Utils_System::url('civicrm/civirule/form/rule', 'action=update&id='.$this->ruleId, TRUE);
      $session->pushUserContext($editUrl);
    }

    if (isset($this->_submitValues['rule_trigger_select'])) {
      $redirectUrl = $this->getTriggerRedirect($this->_submitValues['rule_trigger_select']);
      if ($redirectUrl) {
        CRM_Utils_System::redirect($redirectUrl);
      }
    }

    parent::postProcess();
  }

  /**
   * Function to set default values (overrides parent function)
   * 
   * @return array $defaults
   * @access public
   */
  function setDefaultValues() {
    $defaults = array();
    $defaults['id'] = $this->ruleId;
    switch ($this->_action) {
      case CRM_Core_Action::ADD:
        $this->setAddDefaults($defaults);
        break;
      case CRM_Core_Action::UPDATE:
        $this->setUpdateDefaults($defaults);
        break;
    }
    return $defaults;
  }

  /**
   * Function to add validation rules (overrides parent function)
   * 
   * @access public
   */
  function addRules() {
    if ($this->_action != CRM_Core_Action::DELETE) {
      $this->addFormRule(array(
        'CRM_Civirules_Form_Rule',
        'validateRuleLabelExists'
      ));
    }
    if ($this->_action == CRM_Core_Action::ADD) {
      $this->addFormRule(array('CRM_Civirules_Form_Rule', 'validateTriggerEmpty'));
    }
  }

  /**
   * Function to validate that trigger is not empty in add mode
   *
   * @param array $fields
   * @return array|bool
   * @access static
   */
  static function validateTriggerEmpty($fields) {
    if (empty($fields['rule_trigger_select'])) {
      $errors['rule_trigger_select'] = ts('You have to select a trigger for the rule');
      return $errors;
    }
    return TRUE;
  }

  /**
   * Function to validate if rule label already exists
   *
   * @param array $fields
   * @return array|bool
   * @access static
   */
  static function validateRuleLabelExists($fields) {
    /*
     * if id not empty, edit mode. Check if changed before check if exists
     */
    if (!empty($fields['id']) && $fields['id'] != 'RuleId') {

      /*
       * check if values have changed against database label
       */
      $currentLabel = CRM_Civirules_BAO_Rule::getRuleLabelWithId($fields['id']);
      if ($fields['rule_label'] != $currentLabel &&
        CRM_Civirules_BAO_Rule::labelExists($fields['rule_label']) == TRUE) {
        $errors['rule_label'] = ts('There is already a rule with this name');
        return $errors;
      }
    } else {
      if (CRM_Civirules_BAO_Rule::labelExists($fields['rule_label']) == TRUE) {
        $errors['rule_label'] = ts('There is already a rule with this name');
        return $errors;
      }
    }
    return TRUE;
  }

  /**
   * Function to add the form elements
   * 
   * @access protected
   */
  protected function createFormElements() {
    $this->add('hidden', 'id', ts('RuleId'), array('id' => 'ruleId'));
    if ($this->_action != CRM_Core_Action::DELETE) {
      $this->add('text', 'rule_label', ts('Name'), array('size' => CRM_Utils_Type::HUGE), TRUE);
      $this->add('checkbox', 'rule_is_active', ts('Enabled'));
      $this->add('text', 'rule_created_date', ts('Created Date'));
      $this->add('text', 'rule_created_contact', ts('Created By'));
      $triggerList = array(' - select - ') + CRM_Civirules_Utils::buildTriggerList();
      asort($triggerList);
      $this->add('select', 'rule_trigger_select', ts('Select Trigger'), $triggerList);
      if ($this->_action == CRM_Core_Action::UPDATE) {
        $this->createUpdateFormElements();
      }
    }
    if ($this->_action == CRM_Core_Action::ADD) {
        $this->addButtons(array(
        array('type' => 'next', 'name' => ts('Next'), 'isDefault' => TRUE,),
        array('type' => 'cancel', 'name' => ts('Cancel'))));
    } elseif ($this->_action == CRM_Core_Action::DELETE) {
      $this->addButtons(array(
        array('type' => 'next', 'name' => ts('Delete'), 'isDefault' => TRUE,),
        array('type' => 'cancel', 'name' => ts('Cancel'))));
    } else {
      $this->addButtons(array(
        array('type' => 'next', 'name' => ts('Save'), 'isDefault' => TRUE,),
        array('type' => 'cancel', 'name' => ts('Cancel'))));
    }
  }

  /**
   * Function to add the form elements specific for the update action
   */
  protected function createUpdateFormElements() {
    $this->add('text', 'rule_trigger_label', '', array('size' => CRM_Utils_Type::HUGE));
    $this->assign('ruleConditions', $this->getRuleConditions());
    $this->assign('ruleActions', $this->getRuleActions());
  }

  /**
   * Function to set the form title based on action and data coming in
   * 
   * @access protected
   */
  protected function setFormTitle() {
    $title = 'CiviRules '.  ucfirst(CRM_Core_Action::description($this->_action)).' Rule';
    CRM_Utils_System::setTitle($title);
  }

  /**
   * Function to set default values if action is add
   * 
   * @param array $defaults
   * @access protected
   */
  protected function setAddDefaults(&$defaults) {
    $defaults['rule_is_active'] = 1;
    $defaults['rule_created_date'] = date('d-m-Y');
    $session = CRM_Core_Session::singleton();
    $defaults['rule_created_contact'] = CRM_Civirules_Utils::getContactName($session->get('userID'));
  }

  /**
   * Function to set default values if action is update
   * 
   * @param array $defaults
   * @access protected
   */
  protected function setUpdateDefaults(&$defaults) {
    $ruleData = CRM_Civirules_BAO_Rule::getValues(array('id' => $this->ruleId));
    if (!empty($ruleData) && !empty($this->ruleId)) {
      $defaults['rule_label'] = $ruleData[$this->ruleId]['label'];
      $defaults['rule_is_active'] = $ruleData[$this->ruleId]['is_active'];
      $defaults['rule_created_date'] = date('d-m-Y', 
        strtotime($ruleData[$this->ruleId]['created_date']));
      $defaults['rule_created_contact'] = CRM_Civirules_Utils::
        getContactName($ruleData[$this->ruleId]['created_user_id']);
      if (!empty($ruleData[$this->ruleId]['trigger_id'])) {
        $defaults['rule_trigger_label'] = CRM_Civirules_BAO_Trigger::getTriggerLabelWithId($ruleData[$this->ruleId]['trigger_id']);
      }
    }
  }

  /**
   * Function to get the rule conditions for the rule
   *
   * @return array $ruleConditions
   * @access protected
   */
  protected function getRuleConditions() {
    $conditionParams = array(
      'is_active' => 1,
      'rule_id' => $this->ruleId);
    $ruleConditions = CRM_Civirules_BAO_RuleCondition::getValues($conditionParams);
    foreach ($ruleConditions as $ruleConditionId => $ruleCondition) {
      $conditionClass = CRM_Civirules_BAO_Condition::getConditionObjectById($ruleCondition['condition_id']);
      $conditionClass->setRuleConditionData($ruleCondition);
      $ruleConditions[$ruleConditionId]['name'] = CRM_Civirules_BAO_Condition::getConditionLabelWithId($ruleCondition['condition_id']);
      $ruleConditions[$ruleConditionId]['actions'] = $this->setRuleConditionActions($ruleConditionId, $conditionClass);
      $ruleConditions[$ruleConditionId]['formattedConditionParams'] = $conditionClass->userFriendlyConditionParams();
    }
    return $ruleConditions;
  }

  /**
   * Function to get the rule actions for the rule
   *
   * @return array $ruleActions
   * @access protected
   */
  protected function getRuleActions() {
    $actionParams = array(
      'is_active' => 1,
      'rule_id' => $this->ruleId);
    $ruleActions = CRM_Civirules_BAO_RuleAction::getValues($actionParams);
    foreach ($ruleActions as $ruleActionId => $ruleAction) {
      $actionClass = CRM_Civirules_BAO_Action::getActionObjectById($ruleAction['action_id']);
      $actionClass->setRuleActionData($ruleAction);

      $ruleActions[$ruleActionId]['label'] = CRM_Civirules_BAO_Action::getActionLabelWithId($ruleAction['action_id']);
      $ruleActions[$ruleActionId]['actions'] = $this->setRuleActionActions($ruleActionId, $actionClass);
      $ruleActions[$ruleActionId]['formattedConditionParams'] = $actionClass->userFriendlyConditionParams();

      $ruleActions[$ruleActionId]['formattedDelay'] = '';
      if (!empty($ruleAction['delay'])) {
        $delayClass = unserialize($ruleAction['delay']);
        $ruleActions[$ruleActionId]['formattedDelay'] = $delayClass->getDelayExplanation();
      }
    }
    return $ruleActions;
  }

  /**
   * Function to set the actions for each rule condition
   *
   * @param int $ruleConditionId
   * @param CRM_Civirules_Condition $condition
   * @return array
   * @access protected
   */
  protected function setRuleConditionActions($ruleConditionId, CRM_Civirules_Condition $condition) {
    $conditionActions = array();

    $editUrl = $condition->getExtraDataInputUrl($ruleConditionId);
    if (!empty($editUrl)) {
      $conditionActions[] = '<a class="action-item" title="Edit" href="'.$editUrl.'">'.ts('Edit').'</a>';
    }

    $removeUrl = CRM_Utils_System::url('civicrm/civirule/form/rule_condition', 'reset=1&action=delete&rid='
      .$this->ruleId.'&id='.$ruleConditionId);
    $conditionActions[] = '<a class="action-item" title="Remove" href="'.$removeUrl.'">Remove</a>';
    return $conditionActions;
  }

  /**
   * Function to set the actions for each rule action
   *
   * @param int $ruleActionId
   * @param CRM_Civirules_Action $action
   * @return array
   * @access protected
   */
  protected function setRuleActionActions($ruleActionId, CRM_Civirules_Action $action) {
    $actionActions = array();

    $delaySettingsUrl = CRM_Utils_System::url('civicrm/civirule/form/rule_action', 'reset=1&action=update&rid='
      .$this->ruleId.'&id='.$ruleActionId);
    $actionActions[] = '<a class="action-item" title="Edit delay settings" href="'.$delaySettingsUrl.'">'.ts('Edit delay').'</a>';

    $editUrl = $action->getExtraDataInputUrl($ruleActionId);
    if (!empty($editUrl)) {
      $actionActions[] = '<a class="action-item" title="Edit" href="'.$editUrl.'">'.ts('Edit').'</a>';
    }

    $removeUrl = CRM_Utils_System::url('civicrm/civirule/form/rule_action', 'reset=1&action=delete&rid='
      .$this->ruleId.'&id='.$ruleActionId);
    $actionActions[] = '<a class="action-item" title="Remove" href="'.$removeUrl.'">Remove</a>';
    return $actionActions;
  }

  /**
   * Function to save rule
   *
   * @param array $formValues
   * @param int $userId
   * @access protected
   */
  protected function saveRule($formValues, $userId) {
    if ($this->_action == CRM_Core_Action::ADD) {
      $ruleParams = array(
        'created_date' => date('Ymd'),
        'created_user_id' => $userId);
    } else {
      $ruleParams = array(
        'modified_date' => date('Ymd'),
        'modified_user_id' => $userId,
        'id' => $formValues['id']);
    }
    $ruleParams['label'] = $formValues['rule_label'];
    $ruleParams['name'] = CRM_Civirules_Utils::buildNameFromLabel($formValues['rule_label']);
    $ruleParams['is_active'] = $formValues['rule_is_active'] ? 1 : 0;
    $savedRule = CRM_Civirules_BAO_Rule::add($ruleParams);
    $this->ruleId = $savedRule['id'];
  }

  /**
   * Function to link a trigger to a rule
   *
   * @param array $formValues
   */
  protected function saveRuleTrigger($formValues) {
    if (isset($formValues['rule_trigger_select'])) {
      $ruleParams = array(
        'id' => $this->ruleId,
        'trigger_id' => $formValues['rule_trigger_select']
      );
      CRM_Civirules_BAO_Rule::add($ruleParams);
    }
  }

  /**
   * Returns the url for redirect
   *
   * @param $triggerId
   * @return bool|string url
   */
  protected function getTriggerRedirect($triggerId) {
    $trigger = CRM_Civirules_BAO_Trigger::getTriggerObjectByTriggerId($triggerId, true);
    $redirectUrl = $trigger->getExtraDataInputUrl($this->ruleId);
    if (!empty($redirectUrl)) {
      return $redirectUrl;
    }
    return false;
  }
}
