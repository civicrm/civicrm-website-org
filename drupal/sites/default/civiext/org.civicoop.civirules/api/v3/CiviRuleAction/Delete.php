<?php
/**
 * CiviRuleAction.Create API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRM/API+Architecture+Standards
 */
function _civicrm_api3_civi_rule_action_delete_spec(&$spec) {
  $spec['id']['api_required'] = 0;
}

/**
 * CiviRuleAction.Create API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 */
function civicrm_api3_civi_rule_action_delete($params) {
  CRM_Civirules_BAO_Action::deleteWithId($params['id']);
  $returnValues[$params['id']] = array();
  return civicrm_api3_create_success($returnValues, $params, 'CiviRuleAction', 'Create');
}
