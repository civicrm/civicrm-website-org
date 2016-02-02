<?php
/**
 * Class for CiviRules Condition Contribution Financial Type Form
 *
 * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
 * @license AGPL-3.0
 */

class CRM_CivirulesCronTrigger_Form_GroupMembership extends CRM_CivirulesTrigger_Form_Form {

  /**
   * Method to get groups
   *
   * @return array
   * @access protected
   */
  protected function getGroups() {
    return CRM_Contact_BAO_GroupContact::getGroupList();
  }

  /**
   * Overridden parent method to build form
   *
   * @access public
   */
  public function buildQuickForm() {
    $this->add('hidden', 'rule_id');

    $this->add('select', 'group_id', ts('Groups'), $this->getGroups(), true);

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
    $data = unserialize($this->rule->trigger_params);
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
    $this->rule->trigger_params = serialize($data);
    $this->rule->save();

    parent::postProcess();
  }
}