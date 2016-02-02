<?php

/**
 * Class CRM_CivirulesConditions_Contribution_SpecificAmount
 *
 * This CiviRule condition will check for the xth contribution of a certain amount and financial type
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @link http://redmine.civicoop.org/projects/civirules/wiki/Tutorial_create_a_more_complicated_condition_with_its_own_form_processing
 */

class CRM_CivirulesConditions_Contribution_SpecificAmount extends CRM_Civirules_Condition {

  private $conditionParams = array();
  private $whereClauses = array();
  private $whereParams = array();

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
   * Method to determine if the condition is valid
   *
   * @param CRM_Civirules_TriggerData_TriggerData $triggerData
   * @return bool
   */

  public function isConditionValid(CRM_Civirules_TriggerData_TriggerData $triggerData) {
    $isConditionValid = FALSE;

    $this->buildWhereClauses($triggerData->getEntityData('Contribution'));
    if (!empty($this->whereClauses)) {
      $query = 'SELECT COUNT(*) as countContributions FROM civicrm_contribution WHERE '.implode(' AND ', $this->whereClauses);
      $dao = CRM_Core_DAO::executeQuery($query, $this->whereParams);
      if ($dao->fetch()) {
        switch ($this->conditionParams['count_operator']) {
          case 1:
            if ($dao->countContributions != $this->conditionParams['no_of_contributions']) {
              $isConditionValid = TRUE;
            }
          break;
          case 2:
            if ($dao->countContributions > $this->conditionParams['no_of_contributions']) {
              $isConditionValid = TRUE;
            }
          break;
          case 3:
            if ($dao->countContributions >= $this->conditionParams['no_of_contributions']) {
              $isConditionValid = TRUE;
            }
          break;
          case 4:
            if ($dao->countContributions < $this->conditionParams['no_of_contributions']) {
              $isConditionValid = TRUE;
            }
          break;
          case 5:
            if ($dao->countContributions <= $this->conditionParams['no_of_contributions']) {
              $isConditionValid = TRUE;
            }
          break;
          default:
            if ($dao->countContributions == $this->conditionParams['no_of_contribution']) {
              $isConditionValid = TRUE;
            }
          break;
        }
      }
    }
    return $isConditionValid;
  }
  /**
   * Method to build the where Clauses and related Params
   *
   * @param array $contribution
   * @access protected
   */
  protected function buildWhereClauses($contribution) {
    $this->whereClauses[] = 'contribution_status_id = %1';
    $this->whereParams[1] = array(CRM_Civirules_Utils::getContributionStatusIdWithName('Completed'), 'Integer');
    $this->whereClauses[] = 'is_test = %2';
    $this->whereParams[2] = array(0, 'Integer');
    $this->whereClauses[] = 'contact_id = %3';
    $this->whereParams[3] = array($contribution['contact_id'], 'Integer');
    $index = 3;
    if (!empty($this->conditionParams['amount'])) {
      $index++;
      $this->whereClauses[] = 'total_amount '.$this->setOperator($this->conditionParams['amount_operator']).' %'.$index;
      $this->whereParams[$index] = array($this->conditionParams['amount'], 'Money');
    }
    if (!empty($this->conditionParams['financial_type'])) {
      $index++;
      $this->whereClauses[] = 'financial_type_id = %'.$index;
      $this->whereParams[$index] = array($this->conditionParams['financial_type'], 'Integer');
    }
  }

  /**
   * Method to get the operator
   *
   * @param int $operatorId
   * @return string
   * @access protected
   */
  protected function setOperator($operatorId) {
    switch ($operatorId) {
      case 1:
        return "!=";
      break;
      case 2:
        return ">";
      break;
      case 3:
        return ">=";
      break;
      case 4:
        return "<";
      break;
      case 5:
        return "<=";
      break;
      default:
        return "=";
      break;
    }
  }

  /**
   * Returns a redirect url to extra data input from the user after adding a condition
   *
   * Return false if you do not need extra data input
   *
   * @param int $ruleConditionId
   * @return bool|string
   * @access public
   * @abstract
   */
  public function getExtraDataInputUrl($ruleConditionId) {
    return CRM_Utils_System::url('civicrm/civirule/form/condition/contribution_specificamount/', 'rule_condition_id='.$ruleConditionId);
  }

  /**
   * Returns a user friendly text explaining the condition params
   * e.g. 'Older than 65'
   *
   * @return string
   * @access public
   */
  public function userFriendlyConditionParams() {
    $operator = null;
    $countOperator = $this->setOperator($this->conditionParams['count_operator']);
    $formattedString = 'Number of contributions '.$countOperator.' '.$this->conditionParams['no_of_contributions'];
    if (!empty($this->conditionParams['financial_type'])) {
      $financialType = new CRM_Financial_BAO_FinancialType();
      $financialType->id = $this->conditionParams['financial_type'];
      if ($financialType->find(true)) {
        $formattedString .= ' of financial type ' . $financialType->name;
      }
    }
    $amountOperator = $this->setOperator($this->conditionParams['amount_operator']);
    $formattedString .= ' where amount '.$amountOperator.' '.CRM_Utils_Money::format($this->conditionParams['amount']);
    return $formattedString;
  }

  /**
   * Returns an array with required entity names
   *
   * @return array
   * @access public
   */
  public function requiredEntities() {
    return array('Contribution');
  }

}