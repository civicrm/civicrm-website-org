<?php
/**
 * Utils - class with generic functions CiviRules
 * 
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 */
class CRM_Civirules_Utils {

  /**
   * Function return display name of contact retrieved with contact_id
   * 
   * @param int $contactId
   * @return string $contactName
   * @access public
   * @static
   */
  public static function getContactName($contactId) {
    if (empty($contactId)) {
      return '';
    }
    $params = array(
      'id' => $contactId,
      'return' => 'display_name');
    try {
      $contactName = civicrm_api3('Contact', 'Getvalue', $params);
    } catch (CiviCRM_API3_Exception $ex) {
      $contactName = '';
    }
    return $contactName;
  }

  /**
   * Function to format is_active to yes/no
   * 
   * @param int $isActive
   * @return string
   * @access public
   * @static
   */
  public static function formatIsActive($isActive) {
    if ($isActive == 1) {
      return ts('Yes');
    } else {
      return ts('No');
    }
  }

  /**
   * Public function to generate name from label
   *
   * @param $label
   * @return string
   * @access public
   * @static
   */
  public static function buildNameFromLabel($label) {
    $labelParts = explode(' ', strtolower($label));
    $nameString = implode('_', $labelParts);
    return substr($nameString, 0, 80);
  }

  /**
   * Function to build the trigger list
   *
   * @return array $triggerList
   * @access public
   * @static
   */
  public static function buildTriggerList() {
    $triggerList = array();
    $triggers = CRM_Civirules_BAO_Trigger::getValues(array());
    foreach ($triggers as $triggerId => $trigger) {
      $triggerList[$triggerId] = $trigger['label'];
    }
    return $triggerList;
  }

  /**
   * Function to build the conditions list
   *
   * @return array $conditionList
   * @access public
   * @static
   */
  public static function buildConditionList() {
    $conditionList = array();
    $conditions = CRM_Civirules_BAO_Condition::getValues(array());
    foreach ($conditions as $conditionId => $condition) {
      $conditionList[$conditionId] = $condition['label'];
    }
    return $conditionList;
  }

  /**
   * Function to build the action list
   *
   * @return array $actionList
   * @access public
   * @static
   */
  public static function buildActionList() {
    $actionList = array();
    $actions = CRM_Civirules_BAO_Action::getValues(array());
    foreach ($actions as $actionId => $action) {
      $actionList[$actionId] = $action['label'];
    }
    return $actionList;
  }

  /**
   * Function to return activity type list
   *
   * @return array $activityTypeList
   * @access public
   */
  public static function getActivityTypeList() {
    $activityTypeList = array();
    $activityTypeOptionGroupId = self::getOptionGroupIdWithName('activity_type');
    $params = array(
      'option_group_id' => $activityTypeOptionGroupId,
      'is_active' => 1,
      'options' => array('limit' => 0));
    $activityTypes = civicrm_api3('OptionValue', 'Get', $params);
    foreach ($activityTypes['values'] as $optionValue) {
      $activityTypeList[$optionValue['value']] = $optionValue['label'];
    }
    return $activityTypeList;
  }

  /**
   * Function to get the option group id of an option group with name
   *
   * @param string $optionGroupName
   * @return int $optionGroupId
   * @throws Exception when no option group activity_type is found
   */
  public static function getOptionGroupIdWithName($optionGroupName) {
    $params = array(
      'name' => $optionGroupName,
      'return' => 'id');
    try {
      $optionGroupId = civicrm_api3('OptionGroup', 'Getvalue', $params);
    } catch (CiviCRM_API3_Exception $ex) {
      throw new Exception('Could not find an option group with the name '.$optionGroupName.
        ', error from API OptionGroup Getvalue: '.$ex->getMessage());
    }
    return $optionGroupId;
  }

  /**
   * Function to get option label with value and option group id
   *
   * @param int $optionGroupId
   * @param mixed $optionValue
   * @return array|bool
   * @access public
   * @static
   */
  public static function getOptionLabelWithValue($optionGroupId, $optionValue) {
    if (empty($optionGroupId) or empty($optionValue)) {
      return FALSE;
    } else {
      $params = array(
        'option_group_id' => $optionGroupId,
        'value' => $optionValue,
        'return' => 'label'
      );
      try {
        return civicrm_api3('OptionValue', 'Getvalue', $params);
      } catch (CiviCRM_API3_Exception $ex) {
        return false;
      }
    }
  }

  /**
   * Method to get the contribution status id with name
   *
   * @param string $statusName
   * @return int $statusId
   * @access public
   * @throws Exception when error from API
   * @static
   */
  public static function getContributionStatusIdWithName($statusName) {
    $optionGroupId = self::getOptionGroupIdWithName('contribution_status');
    $optionValueParams = array(
      'option_group_id' => $optionGroupId,
      'name' => $statusName,
      'return' => 'value');
    try {
      $statusId = (int) civicrm_api3('OptionValue', 'Getvalue', $optionValueParams);
    } catch (CiviCRM_API3_Exception $ex) {
      throw new Exception('Could not retrieve a contribution status with name '.
        $statusName.', contact your system administrator. Error from API OptionValue Getvalue: '.$ex->getMessage());
    }
    return $statusId;
  }

  /**
   * Method to get the financial types
   * @return array
   */
  public static function getFinancialTypes() {
    $return = array();
    $dao = CRM_Core_DAO::executeQuery("SELECT * FROM `civicrm_financial_type` where `is_active` = 1");
    while($dao->fetch()) {
      $return[$dao->id] = $dao->name;
    }
    return $return;
  }

  /**
   * Method to get the membership types
   * @param bool $onlyActive
   * @return array
   */
  public static function getMembershipTypes($onlyActive = TRUE) {
    $return = array();
    if ($onlyActive) {
      $params = array('is_active' => 1);
    } else {
      $params = array();
    }
    try {
      $membershipTypes = civicrm_api3("MembershipType", "Get", $params);
      foreach ($membershipTypes['values'] as $membershipType) {
        $return[$membershipType['id']] = $membershipType['name'];
      }
    } catch (CiviCRM_API3_Exception $ex) {}
    return $return;
  }

  /**
   * Method to get the membership status
   * @param bool $onlyActive
   * @return array
   */
  public static function getMembershipStatus($onlyActive = TRUE) {
    $return = array();
    if ($onlyActive) {
      $params = array('is_active' => 1);
    } else {
      $params = array();
    }
    try {
      $apiMembershipStatus = civicrm_api3("MembershipStatus", "Get", $params);
      foreach ($apiMembershipStatus['values'] as $membershipStatus) {
        $return[$membershipStatus['id']] = $membershipStatus['name'];
      }
    } catch (CiviCRM_API3_Exception $ex) {}
    return $return;
  }

  /**
   * Method to check if the incoming date is later than today
   *
   * @param mixed $inDate
   * @return boolean
   * @access public
   * @static
   */
  public static function endDateLaterThanToday($inDate) {
    $isLater = FALSE;
    try {
      $dateToBeChecked = new DateTime($inDate);
      $now = new DateTime();
      if ($dateToBeChecked > $now) {
        $isLater = TRUE;
      }
    } catch (Exception $ex) {}
    return $isLater;
  }
}

