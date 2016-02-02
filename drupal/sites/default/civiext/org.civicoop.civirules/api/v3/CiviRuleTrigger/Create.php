<?php
/**
 * CiviRuleTrigger.Create API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRM/API+Architecture+Standards
 */
function _civicrm_api3_civi_rule_trigger_create_spec(&$spec) {
  $spec['label']['api_required'] = 0;
  $spec['name']['api_required'] = 0;
  $spec['object_name']['api_required'] = 0;
  $spec['op']['api_required'] = 0;
  $spec['class_name']['api_required'] = 0;
}

/**
 * CiviRuleTrigger.Create API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 */
function civicrm_api3_civi_rule_trigger_create($params) {
  $errorMessage = _validateParams($params);
  if (!empty($errorMessage)) {
    return civicrm_api3_create_error($errorMessage);
  }
  $session = CRM_Core_Session::singleton();
  $userId = $session->get('userID');
  if (isset($params['id'])) {
    $params['modified_date'] = date('Ymd');
    $params['modified_user_id'] = $userId;
  } else {
    $params['created_date'] = date('Ymd');
    $params['created_user_id'] = $userId;
  }
  $returnValues = CRM_Civirules_BAO_Trigger::add($params);
  return civicrm_api3_create_success($returnValues, $params, 'CiviRuleTrigger', 'Create');
}

/**
 * Function to validate parameters
 *
 * @param array $params
 * @return string $errorMessage
 */
function _validateParams($params) {
  $errorMessage = '';
  if (!isset($params['id']) && empty($params['label'])) {
    return ts('Label can not be empty when adding a new CiviRule Trigger');
  }
  if (_checkClassNameObjectNameOperation($params) == FALSE) {
    return ts('Either class_name or a combination of object_name and op is mandatory');
  }
  if (isset($params['cron']) && $params['cron'] == 1) {
    $params['object_name'] = null;
    $params['op'] = null;
    if (!isset($params['class_name']) || empty($params['class_name'])) {
      return ts('For a cron type trigger the class_name is mandatory');
    }
  }
  if (isset($params['object_name']) && !empty($params['object_name'])) {
    $extensionConfig = CRM_Civirules_Config::singleton();
    if (!in_array($params['object_name'], $extensionConfig->getValidTriggerObjectNames())) {
      return ts('ObjectName passed in parameters ('.$params['object_name']
        .')is not a valid object for a CiviRule Trigger');
    }
  }
  if (isset($params['op']) && !empty($params['op'])) {
    $extensionConfig = CRM_Civirules_Config::singleton();
    if (!in_array($params['op'], $extensionConfig->getValidTriggerOperations())) {
      return ts('Operation passed in parameters ('.$params['op']
        .')is not a valid operation for a CiviRule Trigger');
    }
  }
  if (CRM_Civirules_BAO_Trigger::triggerExists($params) == TRUE) {
    return ts('There is already a trigger for this class_name or combination of object_name and op');
  }

  return $errorMessage;
}

/**
 * Function to check if className or Op/ObjectName are passed
 *
 * @param array $params
 * @return bool
 */
function _checkClassNameObjectNameOperation($params) {
  if (isset($params['class_name']) && !empty($params['class_name'])) {
    if (!isset($params['object_name']) && !isset($params['op'])) {
      return TRUE;
    } else {
      if (empty($params['object_name']) && empty($params['op'])) {
        return TRUE;
      }
    }
  }
  if (isset($params['object_name']) && isset($params['op']) && !empty($params['object_name']) && !empty($params['op'])) {
    if (!isset($params['class_name']) || empty($params['class_name'])) {
      return TRUE;
    }
  }
  return FALSE;
}

