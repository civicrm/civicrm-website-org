<?php

function _civicrm_api3_contact_authenticate_spec(&$params) {
  $params['username']['api.required'] = 1;
  $params['password']['api.required'] = 1;
  $params['option_limit']['api.default'] = 20;
  $params['return']['api.default'] = "first_name,last_name,email";
}

/**
 * Given a username and password, authenticate the user.
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_contact_authenticate($params) {
  $result = array();

  $auth = user_authenticate($params['username'], $params['password']);

  if (! $auth) {
    throw new API_Exception('Invalid username or password.');
  }

  return civicrm_api3_create_success($result, $params, 'Contact', 'authenticate');
}
