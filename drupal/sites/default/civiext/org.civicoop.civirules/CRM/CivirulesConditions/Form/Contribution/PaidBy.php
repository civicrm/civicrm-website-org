<?php
/**
 * Class for CiviRules Condition Contribution Paid By Form
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @license AGPL-3.0
 */

class CRM_CivirulesConditions_Form_Contribution_PaidBy extends CRM_CivirulesConditions_Form_Form {

  /**
   * Method to get the payment instruments
   *
   * @return array
   * @throws Exception when error in API
   * @access protected
   */
  protected function getPaymentInstruments() {
    $paymentInstruments = array();
    try {
      $optionGroupId = civicrm_api3('OptionGroup', 'Getvalue', array('name' => 'payment_instrument', 'return' => 'id'));
      try {
        $optionValues = civicrm_api3('OptionValue', 'Get', array('option_group_id' => $optionGroupId));
        foreach ($optionValues['values'] as $paymentInstrument) {
          $paymentInstruments[$paymentInstrument['value']] = $paymentInstrument['label'];
        }
        $paymentInstruments[0] = '- select -';
        asort($paymentInstruments);
      } catch (CiviCRM_API3_Exception $ex) {}
    } catch (CiviCRM_API3_Exception $ex) {
      throw new Exception('Option group with name payment_instrument not found,
      error from API OptionGroup Getvalue: '.$ex->getMessage());
    }
    return $paymentInstruments;
  }

  /**
   * Overridden parent method to build form
   *
   * @access public
   */
  public function buildQuickForm() {
    $this->add('hidden', 'rule_condition_id');

    $this->add('select', 'payment_instrument_id', ts('Payment instrument'), $this->getPaymentInstruments(), true);
    $this->add('select', 'operator', ts('Operator'), array('equals', 'is not equal to'), true);

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
    if (!empty($data['payment_instrument_id'])) {
      $defaultValues['payment_instrument_id'] = $data['payment_instrument_id'];
    }
    if (!empty($data['operator'])) {
      $defaultValues['operator'] = $data['operator'];
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
    $data['payment_instrument_id'] = $this->_submitValues['payment_instrument_id'];
    $data['operator'] = $this->_submitValues['operator'];
    $this->ruleCondition->condition_params = serialize($data);
    $this->ruleCondition->save();

    parent::postProcess();
  }
}