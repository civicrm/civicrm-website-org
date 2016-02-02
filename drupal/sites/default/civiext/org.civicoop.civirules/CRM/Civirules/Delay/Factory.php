<?php

class CRM_Civirules_Delay_Factory {

  /**
   * Get a list with all possible delay classes
   *
   * @return array
   */
  public static function getAllDelayClasses() {
    return array(
      new CRM_Civirules_Delay_XMinutes(),
      new CRM_Civirules_Delay_XDays(),
      new CRM_Civirules_Delay_XWeekDay(),
      new CRM_Civirules_Delay_XWeekDayOfMonth(),
    );
  }

  /**
   * Returns the delay class for a given name
   *
   * @param $name
   * @return CRM_Civirules_Delay_Delay
   * @throws Exception
   */
  public static function getDelayClassByName($name) {
    foreach(self::getAllDelayClasses() as $class) {
      if ($class->getName() == $name) {
        return $class;
      }
    }

    throw new Exception('Could not find delay class for '.$name);
  }

  /**
   * Returns an option list of possible delays. This list
   * can be used in a select list
   *
   * Each element has a key which correspondents to the name of the class
   * and the value to the description of the delay
   *
   * @return array
   */
  public static function getOptionList() {
    $classes = self::getAllDelayClasses();
    $options = array();
    foreach($classes as $class) {
      if ($class instanceof CRM_Civirules_Delay_Delay) {
        $options[$class->getName()] = $class->getDescription();
      }
    }
    asort($options);
    return $options;
  }

}