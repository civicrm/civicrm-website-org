<?php
/**
 * Class for CiviRules Condition Contribution Distinct Contributing Day Form
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @license AGPL-3.0
 */

class CRM_CivirulesConditions_Form_Contribution_DistinctContributingDay extends CRM_CivirulesConditions_Form_Form {

  /**
   * Overridden parent method to build form
   *
   * @access public
   */
  public function buildQuickForm() {
    $operatorList[0] = 'equals (=)';
    $operatorList[1] = 'is not equal (!=)';
    $operatorList[2] = 'is more than (>)';
    $operatorList[3] = 'is more than or equal (>=)';
    $operatorList[4] = 'is less than (<)';
    $operatorList[5] = 'is less than or equal (<=)';

    $this->add('hidden', 'rule_condition_id');
    $this->add('select', 'operator', ts('Operator'), $operatorList, true);
    $this->add('text', 'no_of_days', ts('Number of Days'), array(), true);
    $this->addRule('no_of_days','Number of Days must be a whole number','numeric');
    $this->addRule('no_of_days','Number of Days must be a whole number','nopunctuation');

    CRM_CivirulesConditions_Utils_Period::buildQuickForm($this);

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
    $defaultValues = parent::setDefaultValues();
    $data = unserialize($this->ruleCondition->condition_params);
    if (!empty($data['operator'])) {
      $defaultValues['operator'] = $data['operator'];
    }
    if (!empty($data['no_of_days'])) {
      $defaultValues['no_of_days'] = $data['no_of_days'];
    }

    $defaultValues = CRM_CivirulesConditions_Utils_Period::setDefaultValues($defaultValues, $data);

    return $defaultValues;
  }

  public function addRules()
  {
    CRM_CivirulesConditions_Utils_Period::addRules($this);
  }

  /**
   * Overridden parent method to process form data after submission
   *
   * @throws Exception when rule condition not found
   * @access public
   */
  public function postProcess() {
    $data['operator'] = $this->_submitValues['operator'];
    $data['no_of_days'] = $this->_submitValues['no_of_days'];

    $data = CRM_CivirulesConditions_Utils_Period::getConditionParams($this->_submitValues, $data);

    $this->ruleCondition->condition_params = serialize($data);
    $this->ruleCondition->save();

    parent::postProcess();
  }
}