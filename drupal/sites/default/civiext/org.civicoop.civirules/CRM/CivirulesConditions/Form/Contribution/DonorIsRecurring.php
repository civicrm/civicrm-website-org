<?php
/**
 * Class for CiviRules Condition Contribution Donor Is Recurring
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @license AGPL-3.0
 */

class CRM_CivirulesConditions_Form_Contribution_DonorIsRecurring extends CRM_CivirulesConditions_Form_Form {

  /**
   * Overridden parent method to build form
   *
   * @access public
   */
  public function buildQuickForm() {
    $this->add('hidden', 'rule_condition_id');
    $this->addElement('checkbox', 'has_recurring', ts('Donor has recurring contributions?'));
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
    if (!empty($data['has_recurring'])) {
      $defaultValues['has_recurring'] = $data['has_recurring'];
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
    if (isset($this->_submitValues['has_recurring'])) {
      $data['has_recurring'] = $this->_submitValues['has_recurring'];
    } else {
      $data['has_recurring'] = 0;
    }
    $this->ruleCondition->condition_params = serialize($data);
    $this->ruleCondition->save();

    parent::postProcess();
  }
}