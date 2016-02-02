<?php
/**
 * Class for CiviRules Group Contact Action Form
 *
 * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
 * @license AGPL-3.0
 */

class CRM_CivirulesActions_Contact_Form_Subtype extends CRM_CivirulesActions_Form_Form {


  /**
   * Method to get groups
   *
   * @return array
   * @access protected
   */
  protected function getSubtypes() {
    $subTypes = CRM_Contact_BAO_ContactType::contactTypeInfo();
    $options = array();
    foreach($subTypes as $name => $type) {
      if(!empty($type['parent_id'])) {
        $options[$name] = $type['parent_label'].' - '.$type['label'];
      }
    }
    return $options;
  }

  /**
   * Overridden parent method to build the form
   *
   * @access public
   */
  public function buildQuickForm() {
    $this->add('hidden', 'rule_action_id');

    $this->add('select', 'type', ts('Single/Multiple'), array(
      0 => ts('Set one subtype'),
      1 => ts('Set multiple subtypes'),
    ));

    $this->add('select', 'subtype', ts('Contact sub type'), array('' => ts('-- please select --')) + $this->getSubtypes());

    $multiGroup = $this->addElement('advmultiselect', 'subtypes', ts('Contact sub types'), $this->getSubtypes(), array(
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
    $this->addFormRule(array('CRM_CivirulesActions_Contact_Form_Subtype', 'validateSubtype'));
  }

  /**
   * Function to validate value of rule action form
   *
   * @param array $fields
   * @return array|bool
   * @access public
   * @static
   */
  static function validateSubtype($fields) {
    $errors = array();
    if ($fields['type'] == 0 && empty($fields['subtype'])) {
      $errors['subtype'] = ts('You have to select at least one subtype');
    } elseif ($fields['type'] == 1 && (empty($fields['subtypes']) || count($fields['subtypes']) < 1)) {
      $errors['subtypes'] = ts('You have to select at least one subtype');
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
    if (!empty($data['sub_type'])) {
      $defaultValues['sub_type'] = reset($data['sub_type']);
      $defaultValues['sub_types'] = $data['sub_type'];
    }
    if (!empty($data['sub_type']) && count($data['sub_type']) <= 1) {
      $defaultValues['type'] = 0;
    } elseif (!empty($data['sub_type'])) {
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
    $data['sub_type'] = array();
    if ($this->_submitValues['type'] == 0) {
      $data['sub_type'] = array($this->_submitValues['subtype']);
    } else {
      $data['sub_type'] = $this->_submitValues['subtypes'];
    }

    $this->ruleAction->action_params = serialize($data);
    $this->ruleAction->save();
    parent::postProcess();
  }

}