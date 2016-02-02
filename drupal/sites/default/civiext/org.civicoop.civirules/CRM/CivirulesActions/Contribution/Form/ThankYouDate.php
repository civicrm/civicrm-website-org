<?php
/**
 * Class for CiviRules Contribution Thank You Date Form
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @license AGPL-3.0
 */

class CRM_CivirulesActions_Contribution_Form_ThankYouDate extends CRM_CivirulesActions_Form_Form {

  /**
   * Overridden parent method to build the form
   *
   * @access public
   */
  public function buildQuickForm() {
    $this->add('hidden', 'rule_action_id');
    $radioOptions = array('Date of Action Execution', 'xxx days after Action Execution', 'Specific Date');
    $this->addRadio('thank_you_radio', ts('Thank You Date will be set to : '), $radioOptions);
    $this->add('text', 'number_of_days', ts('Number of Days after Action Execution'));
    $this->addDate('thank_you_date', ts('Thank You Date'), FALSE, array('formatType' => 'custom'));
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
    $defaultValues['rule_action_id'] = $this->ruleActionId;
    if (!empty($this->ruleAction->action_params)) {
      $data = unserialize($this->ruleAction->action_params);
    }
    if (!empty($data['number_of_days'])) {
      $defaultValues['number_of_days'] = $data['number_of_days'];
    }
    if (empty($data['thank_you_radio'])) {
      $defaultValues['thank_you_radio'] = 0;
    } else {
      $defaultValues['thank_you_radio'] = $data['thank_you_radio'];
    }
    if (empty($data['thank_you_date'])) {
      list($defaultValues['thank_you_date']) = CRM_Utils_Date::setDateDefaults(date('Y-m-d'));
    } else {
      list($defaultValues['thank_you_date']) = CRM_Utils_Date::setDateDefaults($data['thank_you_date']);
    }
    return $defaultValues;
  }

  /**
   * Overridden parent method to process form data after submitting
   *
   * @access public
   */
  public function postProcess() {
    $data['thank_you_radio'] = $this->_submitValues['thank_you_radio'];
    if ($this->_submitValues['thank_you_radio'] == 2) {
      $data['thank_you_date'] = $this->_submitValues['thank_you_date'];
    } else {
      $data['thank_you_date'] = null;
    }
    if ($this->_submitValues['thank_you_radio'] == 1) {
      $data['number_of_days'] = $this->_submitValues['number_of_days'];
    } else {
      $data['number_of_days'] = 0;
    }
    $this->ruleAction->action_params = serialize($data);
    $this->ruleAction->save();
    parent::postProcess();
  }
}