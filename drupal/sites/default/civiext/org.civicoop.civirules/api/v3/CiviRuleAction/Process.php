<?php

/**
 * CiviRuleAction.process API
 *
 * Process delayed actions
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_civi_rule_action_process($params) {
  $returnValues = CRM_Civirules_Engine::processDelayedActions(60);

  // Spec: civicrm_api3_create_success($values = 1, $params = array(), $entity = NULL, $action = NULL)
  return civicrm_api3_create_success($returnValues, $params, 'CiviRuleAction', 'Process');
}