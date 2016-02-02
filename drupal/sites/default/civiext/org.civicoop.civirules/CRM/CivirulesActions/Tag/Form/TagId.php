<?php
/**
 * Class for CiviRules Group Contact Action Form
 *
 * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
 * @license AGPL-3.0
 */

class CRM_CivirulesActions_Tag_Form_TagId extends CRM_CivirulesActions_Form_Form {


  /**
   * Method to get groups
   *
   * @return array
   * @access protected
   */
  protected function getTags() {
    $tags = CRM_Core_BAO_Tag::getTagsUsedFor();
    $options = array();
    foreach($tags as $tag_id => $tag) {
      $options[$tag_id] = $tag;
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
      0 => ts('Select one tag'),
      1 => ts('Select multiple tags'),
    ));

    $this->add('select', 'tag_id', ts('Tag'), array('' => ts('-- please select --')) + $this->getTags());

    $multiGroup = $this->addElement('advmultiselect', 'tag_ids', ts('Tags'), $this->getTags(), array(
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
    $this->addFormRule(array('CRM_CivirulesActions_Tag_Form_TagId', 'validateTagFields'));
  }

  /**
   * Function to validate value of rule action form
   *
   * @param array $fields
   * @return array|bool
   * @access public
   * @static
   */
  static function validateTagFields($fields) {
    $errors = array();
    if ($fields['type'] == 0 && empty($fields['tag_id'])) {
      $errors['tag_id'] = ts('You have to select at least one tag');
    } elseif ($fields['type'] == 1 && (empty($fields['tag_ids']) || count($fields['tag_ids']) < 1)) {
      $errors['tag_ids'] = ts('You have to select at least one tag');
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
    if (!empty($data['tag_id'])) {
      $defaultValues['tag_id'] = $data['tag_id'];
    }
    if (!empty($data['tag_ids'])) {
      $defaultValues['tag_ids'] = $data['tag_ids'];
    }
    if (!empty($data['tag_ids']) && is_array($data['tag_ids'])) {
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
    $data['tag_id'] = false;
    $data['tag_ids'] = false;
    if ($this->_submitValues['type'] == 0) {
      $data['tag_id'] = $this->_submitValues['tag_id'];
    } else {
      $data['tag_ids'] = $this->_submitValues['tag_ids'];
    }

    $this->ruleAction->action_params = serialize($data);
    $this->ruleAction->save();
    parent::postProcess();
  }

}