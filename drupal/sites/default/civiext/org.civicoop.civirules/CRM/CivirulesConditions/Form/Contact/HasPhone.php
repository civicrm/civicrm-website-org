<?php
/**
 * Class for CiviRules Condition Contribution has contact a tag
 *
 * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
 * @license AGPL-3.0
 */

class CRM_CivirulesConditions_Form_Contact_HasPhone extends CRM_CivirulesConditions_Form_Form {

  protected function getPhoneTypes() {
    return
      array(0 => ts(' - Any phone type -')) +
      CRM_Core_OptionGroup::values('phone_type', false, false, false, false, 'label', false)
    ;
  }

  protected function getLocationTypes() {
    $locTypes = civicrm_api3('LocationType', 'get', array());
    $return[0] = ts('- Any location -');
    foreach($locTypes['values'] as $loc_type) {
      $return[$loc_type['id']] = $loc_type['display_name'];
    }
    return $return;
  }

  /**
   * Overridden parent method to build form
   *
   * @access public
   */
  public function buildQuickForm() {
    $this->add('hidden', 'rule_condition_id');

    $this->add('select', 'location_type', ts('Location type'), $this->getLocationTypes(), true);
    $this->add('select', 'phone_type', ts('Phone type'), $this->getPhoneTypes(), true);

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
    if (!empty($data['phone_type'])) {
      $defaultValues['phone_type'] = $data['phone_type'];
    }
    if (!empty($data['location_type'])) {
      $defaultValues['location_type'] = $data['location_type'];
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
    $data['phone_type'] = $this->_submitValues['phone_type'];
    $data['location_type'] = $this->_submitValues['location_type'];
    $this->ruleCondition->condition_params = serialize($data);
    $this->ruleCondition->save();

    parent::postProcess();
  }
}