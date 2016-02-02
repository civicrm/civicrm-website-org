<?php

class CRM_Civirules_Delay_XMinutes extends CRM_Civirules_Delay_Delay {

  protected $minuteOffset;

  public function delayTo(DateTime $date) {
    $date->modify("+ ".$this->minuteOffset." minutes");
    return $date;
  }

  public function getDescription() {
    return ts('Delay by a number of minutes');
  }

  public function getDelayExplanation() {
    return ts('Delay action by %1 minutes', array(1 => $this->minuteOffset));
  }

  public function addElements(CRM_Core_Form &$form) {
    $form->add('text', 'xminutes_minuteOffset', ts('Minutes'));
  }

  public function validate($values, &$errors) {
    if (empty($values['xminutes_minuteOffset']) || !is_numeric($values['xminutes_minuteOffset'])) {
      $errors['xminutes_minuteOffset'] = ts('You need to provide a number of minutess');
    }
  }

  public function setValues($values) {
    $this->minuteOffset = $values['xminutes_minuteOffset'];
  }

  public function getValues() {
    $values = array();
    $values['xminutes_minuteOffset'] = $this->minuteOffset;
    return $values;
  }

}