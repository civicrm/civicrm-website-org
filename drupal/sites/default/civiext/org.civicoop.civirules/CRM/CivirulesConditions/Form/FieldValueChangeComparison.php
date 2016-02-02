<?php

class CRM_CivirulesConditions_Form_FieldValueChangeComparison extends CRM_CivirulesConditions_Form_Form {

  /**
   * Overridden parent method to perform processing before form is build
   *
   * @access public
   */
  public function preProcess() {
    parent::preProcess();

    if (!$this->conditionClass instanceof CRM_CivirulesConditions_Generic_FieldValueChangeComparison) {
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
    $errors = array();
    $operator = $fields['operator'];
    switch ($operator) {
      case '=':
      case '!=':
      case '>':
      case '>=':
      case '<':
      case '<=':
        if (empty($fields['value'])) {
          $errors['value'] = ts('Compare value is required');
        }
        break;
      case 'is one of':
      case 'is not one of':
      case 'contains one of':
      case 'not contains one of':
      case 'contains all of':
      case 'not contains all of':
        if (empty($fields['multi_value'])) {
          $errors['multi_value'] = ts('Compare values is a required field');
        }
        break;
    }

    $original_operator = $fields['original_operator'];
    switch ($original_operator) {
      case '=':
      case '!=':
      case '>':
      case '>=':
      case '<':
      case '<=':
        if (empty($fields['value'])) {
          $errors['original_value'] = ts('Compare value is required');
        }
        break;
      case 'is one of':
      case 'is not one of':
      case 'contains one of':
      case 'not contains one of':
      case 'contains all of':
      case 'not contains all of':
        if (empty($fields['multi_value'])) {
          $errors['original_multi_value'] = ts('Compare values is a required field');
        }
        break;
    }


    if (count($errors)) {
      return $errors;
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

    $this->add('select', 'original_operator', ts('Operator'), $this->conditionClass->getOperators(), true);
    $this->add('text', 'original_value', ts('Compare value'), true);
    $this->add('textarea', 'original_multi_value', ts('Compare values'));

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

    if (!empty($data['original_operator'])) {
      $defaultValues['original_operator'] = $data['original_operator'];
    }
    if (!empty($data['original_value'])) {
      $defaultValues['original_value'] = $data['original_value'];
    }
    if (!empty($data['original_multi_value'])) {
      $defaultValues['original_multi_value'] = implode("\r\n", $data['original_multi_value']);
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
    $data['original_operator'] = $this->_submitValues['original_operator'];
    $data['original_value'] = $this->_submitValues['original_value'];
    if (isset($this->_submitValues['original_multi_value'])) {
      $data['original_multi_value'] = explode("\r\n", $this->_submitValues['original_multi_value']);
    }

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

}