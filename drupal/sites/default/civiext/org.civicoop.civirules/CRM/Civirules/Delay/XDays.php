<?php

class CRM_Civirules_Delay_XDays extends CRM_Civirules_Delay_Delay {

  protected $dayOffset;

  public function delayTo(DateTime $date) {
    $date->modify("+ ".$this->dayOffset." days");
    return $date;
  }

  public function getDescription() {
    return ts('Delay by a number of days');
  }

  public function getDelayExplanation() {
    return ts('Delay action by %1 days', array(1 => $this->dayOffset));
  }

  public function addElements(CRM_Core_Form &$form) {
    $form->add('text', 'xdays_dayOffset', ts('Days'));
  }

  public function validate($values, &$errors) {
    if (empty($values['xdays_dayOffset']) || !is_numeric($values['xdays_dayOffset'])) {
      $errors['xdays_dayOffset'] = ts('You need to provide a number of days');
    }
  }

  public function setValues($values) {
    $this->dayOffset = $values['xdays_dayOffset'];
  }

  public function getValues() {
    $values = array();
    $values['xdays_dayOffset'] = $this->dayOffset;
    return $values;
  }

}