<?php
/**
 * Class for CiviRules Group Contact Action Form
 *
 * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
 * @license AGPL-3.0
 */

class CRM_CivirulesActions_GroupContact_Form_GroupId extends CRM_CivirulesActions_Form_Form {


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
   * Overridden parent method to build the form
   *
   * @access public
   */
  public function buildQuickForm() {
    $this->add('hidden', 'rule_action_id');

    $this->add('select', 'type', ts('Single/Multiple'), array(
      0 => ts('Select a single a group'),
      1 => ts('Select multiple groups'),
    ));

    $this->add('select', 'group_id', ts('Group'), array('' => ts('-- please select --')) + $this->getGroups());

    $multiGroup = $this->addElement('advmultiselect', 'group_ids', ts('Groups'), $this->getGroups(), array(
      'size' => 5,
      'style' => 'width:250px',
      'class' => 'advmultiselect',
    ));

    $multiGroup->setButtonAttributes('add', array('value' => ts('Add >>')));
    $multiGroup->setButtonAttributes('remove', array('value' => ts('<< Remove')));

    $this->addButtons(array(
      array('type' => 'next', 'name' => ts('Save'), 'isDefault' => TRUE,),
      array('type' => 'cancel', 'name' => ts('Cancel'))));
  }

  public function addRules() {
    $this->addFormRule(array('CRM_CivirulesActions_GroupContact_Form_GroupId', 'validateGroupFields'));
  }

  /**
   * Function to validate value of rule action form
   *
   * @param array $fields
   * @return array|bool
   * @access public
   * @static
   */
  static function validateGroupFields($fields) {
    $errors = array();
    if ($fields['type'] == 0 && empty($fields['group_id'])) {
      $errors['group_id'] = ts('You have to select at least one group');
    } elseif ($fields['type'] == 1 && (empty($fields['group_ids']) || count($fields['group_ids']) < 1)) {
      $errors['group_ids'] = ts('You have to select at least one group');
    }

    if (count($errors)) {
      return $errors;
    }
    return true;
  }

  /**
   * Overridden parent method to set default values
   *
   * @return array $defaultValues
   * @access public
   */
  public function setDefaultValues() {
    $defaultValues = parent::setDefaultValues();
    $data = unserialize($this->ruleAction->action_params);
    if (!empty($data['group_id'])) {
      $defaultValues['group_id'] = $data['group_id'];
    }
    if (!empty($data['group_ids'])) {
      $defaultValues['group_ids'] = $data['group_ids'];
    }
    if (!empty($data['group_ids']) && is_array($data['group_ids'])) {
      $defaultValues['type'] = 1;
    }
    return $defaultValues;
  }

  /**
   * Overridden parent method to process form data after submitting
   *
   * @access public
   */
  public function postProcess() {
    $data['group_id'] = false;
    $data['group_ids'] = false;
    if ($this->_submitValues['type'] == 0) {
      $data['group_id'] = $this->_submitValues['group_id'];
    } else {
      $data['group_ids'] = $this->_submitValues['group_ids'];
    }

    $this->ruleAction->action_params = serialize($data);
    $this->ruleAction->save();
    parent::postProcess();
  }

}