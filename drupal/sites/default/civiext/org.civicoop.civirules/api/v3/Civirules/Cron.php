<?php

/**
 * Civirules.Cron API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRM/API+Architecture+Standards
 */
function _civicrm_api3_civirules_cron_spec(&$spec) {
  //there are no parameters for the civirules cron
}

/**
 * Civirules.Cron API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_civirules_cron($params) {
  $returnValues = array();

  //prevent from crashing with a max execution time error
  set_time_limit(0);

  $rules = CRM_Civirules_BAO_Rule::findRulesForCron();
  foreach($rules as $rule) {
    $return = $rule->process();
    $triggeredEntities = $return['count'];
    $triggeredActions = $return['is_valid_count'];
    $returnValues[$rule->getRuleId()] = array(
      'rule' => CRM_Civirules_BAO_Rule::getRuleLabelWithId($rule->getRuleId()),
      'triggered_entities' => $triggeredEntities,
      'triggered_actions' => $triggeredActions,
    );
  }

  return civicrm_api3_create_success($returnValues, $params, 'Civirules', 'cron');

}

