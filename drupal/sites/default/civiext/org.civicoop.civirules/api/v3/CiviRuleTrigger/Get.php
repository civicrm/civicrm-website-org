<?php
/**
 * CiviRuleTrigger.Get API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_civi_rule_trigger_get($params) {
  $returnValues = CRM_Civirules_BAO_Trigger::getValues($params);
  return civicrm_api3_create_success($returnValues, $params, 'CiviRuleTrigger', 'Get');
}

