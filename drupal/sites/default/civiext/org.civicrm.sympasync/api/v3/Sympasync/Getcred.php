<?php

/**
 * Sympasync.Getcred API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRM/API+Architecture+Standards
 */
function _civicrm_api3_sympasync_getcred_spec(&$spec) {
}

/**
 * Sympasync.Getcred API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_sympasync_getcred($params) {
  // Note: By default, API's require "administer CiviCRM" permission.

  $dsn = CRM_Core_Config::singleton()->dsn;

  $result = array(
    'db_host' => parse_url($dsn, PHP_URL_HOST),
    'db_port' => parse_url($dsn, PHP_URL_PORT),
    'db_name' => ltrim(parse_url($dsn, PHP_URL_PATH), '/'),
    'db_user' => CRM_Core_BAO_Setting::getItem('Sympa', 'sympaSqlUser'),
    'db_pass' => CRM_Core_BAO_Setting::getItem('Sympa', 'sympaSqlPass'),
  );

  if (empty($result['db_user'])) {
    $result['db_user'] = 'civisympa_' . CRM_Utils_String::createRandom(5, CRM_Utils_String::ALPHANUMERIC);
    CRM_Core_BAO_Setting::setItem($result['db_user'], 'Sympa', 'sympaSqlUser');
  }

  if (empty($result['db_pass'])) {
    $result['db_pass'] = CRM_Utils_String::createRandom(24, CRM_Utils_String::ALPHANUMERIC);
    CRM_Core_BAO_Setting::setItem($result['db_pass'], 'Sympa', 'sympaSqlPass');
  }

  return civicrm_api3_create_success($result, $params, 'Sympasync', 'Getcred');
}

