<?php

abstract class CRM_CivirulesConditions_Generic_FieldValueChangeComparison extends CRM_CivirulesConditions_Generic_ValueComparison {

  /**
   * Returns the value of the field for the condition
   * For example: I want to check if age > 50, this function would return the 50
   *
   * @param object CRM_Civirules_TriggerData_TriggerData $triggerData
   * @return
   * @access protected
   * @abstract
   */
  abstract protected function getOriginalFieldValue(CRM_Civirules_TriggerData_TriggerData $triggerData);

  /**
   * Returns the value for the data comparison
   *
   * @return mixed
   * @access protected
   */
  protected function getOriginalComparisonValue() {
    switch ($this->getOperator()) {
      case '=':
      case '!=':
      case '>':
      case '>=':
      case '<':
      case '<=':
        $key = 'original_value';
        break;
      case 'is one of':
      case 'is not one of':
      case 'contains one of':
      case 'not contains one of':
      case 'contains all of':
      case 'not contains all of':
        $key = 'original_multi_value';
        break;
    }

    if (!empty($this->conditionParams[$key])) {
      return $this->conditionParams[$key];
    } else {
      return '';
    }
  }

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
  protected function getOriginalOperator() {
    if (!empty($this->conditionParams['original_operator'])) {
      return $this->conditionParams['original_operator'];
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
    //not the right trigger. The trigger data should contain also
    if (!$triggerData instanceof CRM_Civirules_TriggerData_Interface_OriginalData) {
      return false;
    }

    $originalValue = $this->getOriginalFieldValue($triggerData);
    $originalCompareValue = $this->getOriginalComparisonValue();
    $originalComparison = $this->compare($originalValue, $originalCompareValue, $this->getOriginalOperator());

    $value = $this->getFieldValue($triggerData);
    $compareValue = $this->getComparisonValue();
    $newComparison = $this->compare($value, $compareValue, $this->getOperator());

    if ($originalComparison && $newComparison) {
      return true;
    }
    return false;
  }

  public function getExtraDataInputUrl($ruleConditionId) {
    return CRM_Utils_System::url('civicrm/civirule/form/condition/datachangedcomparison/', 'rule_condition_id='.$ruleConditionId);
  }

  /**
   * Returns a user friendly text explaining the condition params
   * e.g. 'Older than 65'
   *
   * @return string
   * @access public
   */
  public function userFriendlyConditionParams() {
    $originalComparisonValue = $this->getOriginalComparisonValue();
    if (is_array($originalComparisonValue)) {
      $originalComparisonValue = implode(", ", $originalComparisonValue);
    }
    $comparisonValue = $this->getComparisonValue();
    if (is_array($comparisonValue)) {
      $comparisonValue = implode(", ", $comparisonValue);
    }
    return
      ts('Old value  ').
      htmlentities(($this->getOriginalOperator())).' '.htmlentities($originalComparisonValue).'&nbsp'.
      ts ('and new value ').
      htmlentities(($this->getOperator())).' '.htmlentities($comparisonValue);
  }

}