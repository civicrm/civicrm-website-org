<?php

class CRM_CivirulesConditions_Form_Contribution_TotalContributedAmount extends CRM_CivirulesConditions_Form_ValueComparison {

  /**
   * Overridden parent method to build form
   *
   * @access public
   */
  public function buildQuickForm() {
    parent::buildQuickForm();

    CRM_CivirulesConditions_Utils_Period::buildQuickForm($this);
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
  public function postProcess()
  {
    $data = unserialize($this->ruleCondition->condition_params);
    $data = CRM_CivirulesConditions_Utils_Period::getConditionParams($this->_submitValues, $data);

    $this->ruleCondition->condition_params = serialize($data);
    $this->ruleCondition->save();

    parent::postProcess();
  }

}