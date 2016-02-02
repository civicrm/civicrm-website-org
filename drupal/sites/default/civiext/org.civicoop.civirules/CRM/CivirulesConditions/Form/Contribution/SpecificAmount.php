<?php
/**
 * Class for CiviRules Condition Contribution Count Number of Contributions with a Specific Amount
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @license AGPL-3.0
 */

class CRM_CivirulesConditions_Form_Contribution_SpecificAmount extends CRM_CivirulesConditions_Form_Form {

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

    $financialTypes = CRM_Civirules_Utils::getFinancialTypes();
    $financialTypes[0] = ts(' - any -');
    asort($financialTypes);

    $this->add('hidden', 'rule_condition_id');
    $this->add('select', 'count_operator', ts('Operator'), $operatorList, true);
    $this->add('select', 'amount_operator', ts('where Operator'), $operatorList, true);
    $this->add('select', 'financial_type', ts('of Financial Type'), $financialTypes, true);
    $this->add('text', 'no_of_contributions', ts('Number of Contributions'), array(), true);
    $this->addRule('no_of_contributions','Number of contributions must be a whole number','numeric');
    $this->addRule('no_of_contributions','Number of contributions must be a whole number','nopunctuation');
    $this->add('text', 'amount', ts('Amount'), array(), true);
    $this->addRule('amount','Amount can only contain numbers','numeric');

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
    if (!empty($data['count_operator'])) {
      $defaultValues['count_operator'] = $data['count_operator'];
    }
    if (!empty($data['no_of_contributions'])) {
      $defaultValues['no_of_contributions'] = $data['no_of_contributions'];
    }
    if (!empty($data['financial_type'])) {
      $defaultValues['financial_type'] = $data['financial_type'];
    }
    if (!empty($data['amount_operator'])) {
      $defaultValues['amount_operator'] = $data['amount_operator'];
    }
    if (!empty($data['amount'])) {
      $defaultValues['amount'] = $data['amount'];
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
    $data['count_operator'] = $this->_submitValues['count_operator'];
    $data['no_of_contributions'] = $this->_submitValues['no_of_contributions'];
    $data['financial_type'] = $this->_submitValues['financial_type'];
    $data['amount_operator'] = $this->_submitValues['amount_operator'];
    $data['amount'] = $this->_submitValues['amount'];
    $this->ruleCondition->condition_params = serialize($data);
    $this->ruleCondition->save();

    parent::postProcess();
  }
}