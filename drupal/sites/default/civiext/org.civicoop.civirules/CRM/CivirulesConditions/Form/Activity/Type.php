<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_CivirulesConditions_Form_Activity_Type extends CRM_CivirulesConditions_Form_Form {

  /**
   * Overridden parent method to build the form
   *
   * @access public
   */
  public function buildQuickForm() {
    $this->add('hidden', 'rule_condition_id');

    $activityTypeList = array('- select -') + CRM_Civirules_Utils::getActivityTypeList();
    asort($activityTypeList);
    $this->add('select', 'activity_type_id', 'Activity Type', $activityTypeList, true);

    $this->addButtons(array(
      array('type' => 'next', 'name' => ts('Save'), 'isDefault' => TRUE,),
      array('type' => 'cancel', 'name' => ts('Cancel'))));

    parent::buildQuickForm();
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
    if (!empty($data['activity_type_id'])) {
      $defaultValues['activity_type_id'] = $data['activity_type_id'];
    }
    return $defaultValues;
  }

  /**
   * Overridden parent method to perform data processing once form is submitted
   *
   * @access public
   */
  public function postProcess() {
    $data['activity_type_id'] = $this->_submitValues['activity_type_id'];
    $this->ruleCondition->condition_params = serialize($data);
    $this->ruleCondition->save();

    parent::postProcess();
  }

}