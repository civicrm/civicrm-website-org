<?php
/**
 * Abstract class for generic value comparison conditions
 *
 * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
 * @license AGPL-3.0
 */

abstract class CRM_CivirulesConditions_Generic_ValueComparison extends CRM_Civirules_Condition {

  protected $conditionParams = array();

  /**
   * Method to set the Rule Condition data
   *
   * @param array $ruleCondition
   * @access public
   */
  public function setRuleConditionData($ruleCondition) {
    parent::setRuleConditionData($ruleCondition);
    $this->conditionParams = array();
    if (!empty($this->ruleCondition['condition_params'])) {
      $this->conditionParams = unserialize($this->ruleCondition['condition_params']);
    }
  }

  /**
   * Returns an array with all possible options for the field, in
   * case the field is a select field, e.g. gender, or financial type
   * Return false when the field is a select field
   *
   * This method could be overriden by child classes to return the option
   *
   * The return is an array with the field option value as key and the option label as value
   *
   * @return bool
   */
  public function getFieldOptions() {
    return false;
  }

  /**
   * Returns true when the field is a select option with multiple select
   *
   * @see getFieldOptions
   * @return bool
   */
  public function isMultiple() {
    return false;
  }

  /**
   * Returns the value of the field for the condition
   * For example: I want to check if age > 50, this function would return the 50
   *
   * @param object CRM_Civirules_TriggerData_TriggerData $triggerData
   * @return
   * @access protected
   * @abstract
   */
  abstract protected function getFieldValue(CRM_Civirules_TriggerData_TriggerData $triggerData);

  /**
   * Returns the value for the data comparison
   *
   * @return mixed
   * @access protected
   */
  protected function getComparisonValue() {
    switch ($this->getOperator()) {
      case '=':
      case '!=':
      case '>':
      case '>=':
      case '<':
      case '<=':
        $key = 'value';
        break;
      case 'is one of':
      case 'is not one of':
      case 'contains one of':
      case 'not contains one of':
      case 'contains all of':
      case 'not contains all of':
        $key = 'multi_value';
        break;
    }

    if (!empty($this->conditionParams[$key])) {
      return $this->conditionParams[$key];
    } else {
      return '';
    }
  }

  /**
   * Returns an operator for comparison
   *
   * Valid operators are:
   * - equal: =
   * - not equal: !=
   * - greater than: >
   * - lesser than: <
   * - greater than or equal: >=
   * - lesser than or equal: <=
   *
   * @return string operator for comparison
   * @access protected
   */
  protected function getOperator() {
    if (!empty($this->conditionParams['operator'])) {
      return $this->conditionParams['operator'];
    } else {
      return '';
    }
  }

  /**
   * Mandatory method to return if the condition is valid
   *
   * @param object CRM_Civirules_TriggerData_TriggerData $triggerData
   * @return bool
   * @access public
   */

  public function isConditionValid(CRM_Civirules_TriggerData_TriggerData $triggerData) {
    $value = $this->getFieldValue($triggerData);
    $compareValue = $this->getComparisonValue();
    return $this->compare($value, $compareValue, $this->getOperator());
  }

  /**
   * Method to compare data
   *
   * @param mixed $leftValue
   * @param mixed $rightValue
   * @param string $operator
   * @return bool
   * @access protected
   */
  protected function compare($leftValue, $rightValue, $operator) {
    switch ($operator) {
      case '=':
        if ($leftValue == $rightValue) {
          return true;
        } else {
          return false;
        }
        break;
      case '>':
        if ($leftValue > $rightValue) {
          return true;
        } else {
          return false;
        }
        break;
      case '<':
        if ($leftValue < $rightValue) {
          return true;
        } else {
          return false;
        }
        break;
      case '>=':
        if ($leftValue >= $rightValue) {
          return true;
        } else {
          return false;
        }
        break;
      case '<=':
        if ($leftValue <= $rightValue) {
          return true;
        } else {
          false;
        }
        break;
      case '!=':
        if ($leftValue != $rightValue) {
          return true;
        } else {
          return false;
        }
        break;
      case 'is one of':
        $rightArray = $this->convertValueToArray($rightValue);
        if (in_array($leftValue, $rightArray)) {
          return true;
        }
        return false;
        break;
      case 'is not one of':
        $rightArray = $this->convertValueToArray($rightValue);
        if (!in_array($leftValue, $rightArray)) {
          return true;
        }
        return false;
        break;
      case 'contains one of':
        $leftArray = $this->convertValueToArray($leftValue);
        $rightArray = $this->convertValueToArray($rightValue);
        if ($this->containsOneOf($leftArray, $rightArray)) {
          return true;
        }
        return false;
        break;
      case 'not contains one of':
        $leftArray = $this->convertValueToArray($leftValue);
        $rightArray = $this->convertValueToArray($rightValue);
        if (!$this->containsOneOf($leftArray, $rightArray)) {
          return true;
        }
        return false;
        break;
      case 'contains all of':
        $leftArray = $this->convertValueToArray($leftValue);
        $rightArray = $this->convertValueToArray($rightValue);
        if ($this->containsAllOf($leftArray, $rightArray)) {
          return true;
        }
        return false;
        break;
      case 'not contains all of':
        $leftArray = $this->convertValueToArray($leftValue);
        $rightArray = $this->convertValueToArray($rightValue);
        if ($this->notContainsAllOf($leftArray, $rightArray)) {
          return true;
        }
        return false;
        break;
      default:
        return false;
        break;
    }
    return false;
  }

  protected function containsOneOf($leftValues, $rightValues) {
    foreach($leftValues as $leftvalue) {
      if (in_array($leftvalue, $rightValues)) {
        return true;
      }
    }
    return false;
  }

  protected function containsAllOf($leftValues, $rightValues) {
    $foundValues = array();
    foreach($leftValues as $leftVaue) {
      if (in_array($leftVaue, $rightValues)) {
        $foundValues[] = $leftVaue;
      }
    }
    if (count($foundValues) > 0 && count($foundValues) == count($rightValues)) {
      return true;
    }
    return false;
  }

  protected function notContainsAllOf($leftValues, $rightValues) {
    foreach($rightValues as $rightValue) {
      if (in_array($rightValue, $leftValues)) {
        return false;
      }
    }
    return true;
  }

  /**
   * Converts a string to an array, the delimeter is the CRM_Core_DAO::VALUE_SEPERATOR
   *
   * This function could be overriden by child classes to define their own array
   * seperator
   *
   * @param $value
   * @return array
   */
  protected function convertValueToArray($value) {
    if (is_array($value)) {
      return $value;
    }
    //split on new lines
    return explode(CRM_Core_DAO::VALUE_SEPARATOR, $value);
  }

  /**
   * Returns a redirect url to extra data input from the user after adding a condition
   *
   * Return false if you do not need extra data input
   *
   * @param int $ruleConditionId
   * @return bool|string
   * @access public
   */
  public function getExtraDataInputUrl($ruleConditionId) {
    return CRM_Utils_System::url('civicrm/civirule/form/condition/datacomparison/', 'rule_condition_id='.$ruleConditionId);
  }

  /**
   * Returns a user friendly text explaining the condition params
   * e.g. 'Older than 65'
   *
   * @return string
   * @access public
   */
  public function userFriendlyConditionParams() {
    return htmlentities(($this->getOperator())).' '.htmlentities($this->getComparisonValue());
  }

  /**
   * Returns an array with possible operators
   *
   * @return array
   */
  public function getOperators() {
    return array(
      '=' => ts('Is equal to'),
      '!=' => ts('Is not equal to'),
      '>' => ts('Is greater than'),
      '<' => ts('Is less than'),
      '>=' => ts('Is greater than or equal to'),
      '<=' => ts('Is less than or equal to'),
      'is one of' => ts('Is one of'),
      'is not one of' => ts('Is not one of'),
      'contains one of' => ts('Does contain one of'),
      'not contains one of' => ts('Does not contain one of'),
      'contains all of' => ts('Does contain all of'),
      'not contains all of' => ts('Does not contain all of'),
    );
  }

}