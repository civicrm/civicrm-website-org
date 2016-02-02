<?php

class CRM_Civirules_Delay_XWeekDay extends CRM_Civirules_Delay_Delay {

  protected $week_offset;

  protected $day;

  protected $time_hour = '9';

  protected $time_minute = '00';

  public function delayTo(DateTime $date) {
    $d = clone $date;
    $d->modify('-30 minutes');
    $mod = $this->day.' this week';
    $date->modify($mod);
    $date->setTime((int) $this->time_hour, (int) $this->time_minute);
    if ($date <= $d) {
      $date->modify('+1 week');
      $date->modify($mod);
    }
    $weeknr = $date->format("W");
    switch ($this->week_offset) {
      case 'odd':
        if(!($weeknr&1)) {
          $date->modify('+1 week');
          $date->modify($mod);
        }
        break;
      case 'even':
        if($weeknr&1) {
          $date->modify('+1 week');
          $date->modify($mod);
        }
        break;
    }

    return $date;
  }

  public function getDescription() {
    return ts('Day of week');
  }

  public function getDelayExplanation() {
    $offsets = $this->getWeekOffset();
    return ts('Delay action to %1 of %2 at %3:%4',
      array(
        1 => ts($this->day),
        2 => $offsets[$this->week_offset],
        3 => $this->time_hour,
        4 => $this->time_minute < 10 && strlen($this->time_minute) <= 1 ? '0'.$this->time_minute : $this->time_minute,
      ));
  }

  public function addElements(CRM_Core_Form &$form) {
    $form->add('select', 'XWeekDay_week_offset', ts('Offset'), $this->getWeekOffset());
    $form->add('select', 'XWeekDay_day', ts('Days'), $this->getDays());
    $form->add('text', 'XWeekDay_time_hour', ts('Time (hour)'));
    $form->add('text', 'XWeekDay_time_minute', ts('Time (minute)'));
  }

  protected function getDays() {
    return array(
      'sunday' => ts('Sunday'),
      'monday' => ts('Monday'),
      'tuesday' => ts('Tuesday'),
      'wednesday' => ts('Wednesday'),
      'thursday' => ts('Thursday'),
      'friday' => ts('Friday'),
      'saturday' => ts('Saturday'),
    );
  }

  protected function getWeekOffset() {
    return array(
      'every' => ts('Every week'),
      'even' => ts('Even weeks'),
      'odd' => ts('Odd weeks'),
    );
  }

  public function validate($values, &$errors) {
    if (empty($values['XWeekDay_time_hour']) || !is_numeric($values['XWeekDay_time_hour']) || $values['XWeekDay_time_hour'] < 0 || $values['XWeekDay_time_hour'] > 23) {
      $errors['XWeekDay_time_hour'] = ts('You need to provide a number between 0 and 23');
    }
    if (empty($values['XWeekDay_time_minute']) || !is_numeric($values['XWeekDay_time_minute']) || $values['XWeekDay_time_minute'] < 0 || $values['XWeekDay_time_minute'] > 59) {
      $errors['XWeekDay_time_minute'] = ts('You need to provide a number between 0 and 59');
    }
  }

  public function setValues($values) {
    $this->week_offset = $values['XWeekDay_week_offset'];
    $this->day = $values['XWeekDay_day'];
    $this->time_hour = $values['XWeekDay_time_hour'];
    $this->time_minute = $values['XWeekDay_time_minute'];
  }

  public function getValues() {
    $values = array();
    $values['XWeekDay_week_offset'] = $this->week_offset;
    $values['XWeekDay_day'] = $this->day;
    $values['XWeekDay_time_hour'] = $this->time_hour;
    $values['XWeekDay_time_minute'] = $this->time_minute;
    return $values;
  }

  /**
   * Set default values
   *
   * @param $values
   */
  public function setDefaultValues(&$values) {
    $values['XWeekDay_time_hour'] = '9';
    $values['XWeekDay_time_minute'] = '00';
  }

}