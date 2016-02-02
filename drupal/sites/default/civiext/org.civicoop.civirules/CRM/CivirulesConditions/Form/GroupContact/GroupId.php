<?php
/**
 * Class for CiviRules ValueComparison Form
 *
 * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
 * @license AGPL-3.0
 */

class CRM_CivirulesConditions_Form_GroupContact_GroupId extends CRM_CivirulesConditions_Form_Form {

  /**
   * Method to get groups
   *
   * @return array
   * @access protected
   */
  protected function getGroups() {
    return array('' => ts('-- please select --')) + CRM_Contact_BAO_GroupContact::getGroupList();
  }

  /**
   * Overridden parent method to build form
   *
   * @access public
   */
  public function buildQuickForm() {
    $this->add('hidden', 'rule_condition_id');

    $this->add('select', 'group_id', ts('Group'), $this->getGroups(), true);

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
    if (!empty($data['group_id'])) {
      $defaultValues['group_id'] = $data['group_id'];
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
    $data['group_id'] = $this->_submitValues['group_id'];
    $this->ruleCondition->condition_params = serialize($data);
    $this->ruleCondition->save();

    parent::postProcess();
  }
}