<?php
/**
 * Class for CiviRules ValueComparison Form
 *
 * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
 * @license AGPL-3.0
 */

class CRM_CivirulesConditions_Form_ValueComparison extends CRM_CivirulesConditions_Form_Form {

  /**
   * Overridden parent method to perform processing before form is build
   *
   * @access public
   */
  public function preProcess() {
    parent::preProcess();

    if (!$this->conditionClass instanceof CRM_CivirulesConditions_Generic_ValueComparison) {
      throw new Exception("Not a valid value comparison class");
    }
  }

  /**
   * Function to add validation condition rules (overrides parent function)
   *
   * @access public
   */
  public function addRules()
  {
    $this->addFormRule(array('CRM_CivirulesConditions_Form_ValueComparison', 'validateOperatorAndComparisonValue'));
  }

  public static function validateOperatorAndComparisonValue($fields) {
    $operator = $fields['operator'];
    switch ($operator) {
      case '=':
      case '!=':
      case '>':
      case '>=':
      case '<':
      case '<=':
        if (empty($fields['value'])) {
          return array('value' => ts('Compare value is required'));
        }
        break;
      case 'is one of':
      case 'is not one of':
      case 'contains one of':
      case 'not contains one of':
      case 'contains all of':
      case 'not contains all of':
        if (empty($fields['multi_value'])) {
          return array('multi_value' => 'Compare values is a required field');
        }
        break;
    }
    return true;
  }

  /**
   * Overridden parent method to build form
   *
   * @access public
   */
  public function buildQuickForm() {
    $this->setFormTitle();

    $this->add('hidden', 'rule_condition_id');

    $this->add('select', 'operator', ts('Operator'), $this->conditionClass->getOperators(), true);
    $this->add('text', 'value', ts('Compare value'), true);
    $this->add('textarea', 'multi_value', ts('Compare values'));

    $this->assign('field_options', $this->conditionClass->getFieldOptions());
    $this->assign('is_field_option_multiple', $this->conditionClass->isMultiple());

    $this->addButtons(array(
      array('type' => 'next', 'name' => ts('Save'), 'isDefault' => TRUE,),
      array('type' => 'cancel', 'name' => ts('Cancel'))));
  }

  /**
   * Overridden parent method to set default values
   *
   * @return array $defaultValues
   * @access public
   */
  public function setDefaultValues() {
    $data = array();
    $defaultValues = array();
    $defaultValues['rule_condition_id'] = $this->ruleConditionId;
    $ruleCondition = new CRM_Civirules_BAO_RuleCondition();
    $ruleCondition->id = $this->ruleConditionId;
    if ($ruleCondition->find(true)) {
      $data = unserialize($ruleCondition->condition_params);
    }
    if (!empty($data['operator'])) {
      $defaultValues['operator'] = $data['operator'];
    }
    if (!empty($data['value'])) {
      $defaultValues['value'] = $data['value'];
    }
    if (!empty($data['multi_value'])) {
      $defaultValues['multi_value'] = implode("\r\n", $data['multi_value']);
    }
    return $defaultValues;
  }

  /**
   * Overridden parent method to process form data after submission
   *
   * @throws Exception when rule condition not found
   * @access public
   */
  public function postProcess() {
    $data = unserialize($this->ruleCondition->condition_params);
    $data['operator'] = $this->_submitValues['operator'];
    $data['value'] = $this->_submitValues['value'];
    if (isset($this->_submitValues['multi_value'])) {
      $data['multi_value'] = explode("\r\n", $this->_submitValues['multi_value']);
    }
    $this->ruleCondition->condition_params = serialize($data);
    $this->ruleCondition->save();

    $session = CRM_Core_Session::singleton();
    $session->setStatus('Condition '.$this->condition->label.' parameters updated to CiviRule '
      .CRM_Civirules_BAO_Rule::getRuleLabelWithId($this->ruleCondition->rule_id),
      'Condition parameters updated', 'success');

    $redirectUrl = CRM_Utils_System::url('civicrm/civirule/form/rule', 'action=update&id='.$this->ruleCondition->rule_id, TRUE);
    CRM_Utils_System::redirect($redirectUrl);
  }

  /**
   * Method to set the form title
   *
   * @access protected
   */
  protected function setFormTitle() {
    $conditionLabel = '';
    $ruleCondition = new CRM_Civirules_BAO_RuleCondition();
    $ruleCondition->id = $this->ruleConditionId;
    if ($ruleCondition->find(true)) {
      $condition = new CRM_Civirules_BAO_Condition();
      $condition->id = $ruleCondition->condition_id;
      if ($condition->find(true)) {
        $conditionLabel = $condition->label;
      }
    }

    $title = 'CiviRules Edit Condition parameters';
    $this->assign('ruleConditionHeader', 'Edit Condition '.$conditionLabel.' of CiviRule '
      .CRM_Civirules_BAO_Rule::getRuleLabelWithId($ruleCondition->rule_id));
    CRM_Utils_System::setTitle($title);
  }
}