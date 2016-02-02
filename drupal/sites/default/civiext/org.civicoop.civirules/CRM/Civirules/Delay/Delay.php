<?php

abstract class CRM_Civirules_Delay_Delay {

  /**
   * Returns the DateTime to which an action is delayed to
   *
   * @param DateTime $date
   * @return DateTime
   */
  abstract public function delayTo(DateTime $date);

  /**
   * Add elements to the form
   *
   * @param \CRM_Core_Form $form
   * @return mixed
   */
  abstract public function addElements(CRM_Core_Form &$form);

  /**
   * Validate the values and set error message in $errors
   *
   * @param array $values
   * @param array $errors
   * @return void
   */
  abstract public function validate($values, &$errors);

  /**
   * Set the values
   *
   * @param array $values
   * @return void
   */
  abstract public function setValues($values);

  /**
   * Get the values
   *
   * @return array
   */
  abstract public function getValues();

  /**
   * Returns an description of the delay
   *
   * @return string
   */
  abstract public function getDescription();

  /**
   * Returns an explanation of the delay
   *
   * @return string
   */
  public function getDelayExplanation() {
    return $this->getDescription();
  }

  /**
   * Set default values
   *
   * @param $values
   */
  public function setDefaultValues(&$values) {

  }

  /**
   * Returns the name of the template
   *
   * @return string
   */
  public function getTemplateFilename() {
    return str_replace('_',
        DIRECTORY_SEPARATOR,
        CRM_Utils_System::getClassName($this)
      ) . '.tpl';
  }

  /**
   * Returns the name
   *
   * @return string
   */
  public function getName() {
    return get_class($this);
  }

}