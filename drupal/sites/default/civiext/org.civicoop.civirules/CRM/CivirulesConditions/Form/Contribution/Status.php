<?php
/**
 * Class for CiviRules ValueComparison Form
 *
 * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
 * @license AGPL-3.0
 */

class CRM_CivirulesConditions_Form_Contribution_Status extends CRM_CivirulesConditions_Form_Form {

  protected function getContributionStatus() {
    return array('' => ts('-- please select --')) + CRM_Core_BAO_OptionValue::getOptionValuesAssocArrayFromName('contribution_status');
  }

  /**
   * Overridden parent method to build form
   *
   * @access public
   */
  public function buildQuickForm() {
    $this->add('hidden', 'rule_condition_id');

    $this->add('select', 'contribution_status_id', ts('Status'), $this->getContributionStatus(), true);

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
    if (!empty($data['contribution_status_id'])) {
      $defaultValues['contribution_status_id'] = $data['contribution_status_id'];
    }
    return $defaultValues;
  }

  /**
   * Overridden parent method to process form data after submission
   *
   * @throws Exception when rule condition not found
   * @access public
   */
  public function postProcess()
  {
    $data['contribution_status_id'] = $this->_submitValues['contribution_status_id'];
    $this->ruleCondition->condition_params = serialize($data);
    $this->ruleCondition->save();

    parent::postProcess();
  }

}