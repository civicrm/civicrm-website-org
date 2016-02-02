<?php
/**
 * CiviRuleCondition.Create API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRM/API+Architecture+Standards
 */
function _civicrm_api3_civi_rule_condition_create_spec(&$spec) {
  $spec['label']['api_required'] = 0;
  $spec['name']['api_required'] = 0;
  $spec['id']['api_required'] = 0;
  $spec['class_name']['api_required'] = 0;
}

/**
 * CiviRuleCondition.Create API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 */
function civicrm_api3_civi_rule_condition_create($params) {
  if (!isset($params['id']) && empty($params['label'])) {
    return civicrm_api3_create_error('Label can not be empty when adding a new CiviRule Condition');
  }
  if (empty($params['class_name']) && !isset($params['id'])) {
    return civicrm_api3_create_error('Class_name can not be empty');
  }
  /*
   * set created or modified date and user_id
   */
  $session = CRM_Core_Session::singleton();
  $userId = $session->get('userID');
  if (isset($params['id'])) {
    $params['modified_date'] = date('Ymd');
    $params['modified_user_id'] = $userId;
  } else {
    $params['created_date'] = date('Ymd');
    $params['created_user_id'] = $userId;
  }
  $returnValues = CRM_Civirules_BAO_Condition::add($params);
  return civicrm_api3_create_success($returnValues, $params, 'CiviRuleCondition', 'Create');
}

