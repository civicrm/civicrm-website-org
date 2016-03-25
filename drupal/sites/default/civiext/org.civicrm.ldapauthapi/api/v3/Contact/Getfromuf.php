<?php

function _civicrm_api3_contact_getfromuf_spec(&$params) {
  # $params['username']['api.required'] = 1;
  $params['option_limit']['api.default'] = 20;
  $params['return']['api.default'] = "first_name,last_name,email";
}

/**
 * Given a username and password, getfromuf the user.
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_contact_getfromuf($params) {
  $result = array();

  if (! empty($params['username'])) {
    $uf_id = db_query('SELECT uid FROM {users} WHERE name = :name', array(':name' => $params['username']))->fetchField();

    if (! $uf_id) {
      throw new API_Exception('Invalid username.');
    }

    $api = civicrm_api3('UFMatch', 'get', array(
      'uf_id' => $uf_id,
      'sequential' => 1,
      'api.Contact.get' => array(
        'id' => '$value.contact_id',
        'return.display_name' => 1,
        'return.first_name' => 1,
        'return.last_name' => 1,
        'return.email' => 1,
        'return.title' => 1,
        'return.organization_name' => 1,
        'return.job_title' => 1,
      ),
    ));

    $result = array(
      0 => array(
        'display_name' => $api['values'][0]['api.Contact.get']['values'][0]['display_name'],
        'first_name' => $api['values'][0]['api.Contact.get']['values'][0]['first_name'],
        'last_name' => $api['values'][0]['api.Contact.get']['values'][0]['last_name'],
        'email' => $api['values'][0]['api.Contact.get']['values'][0]['email'],
        'contact_id' => $api['values'][0]['api.Contact.get']['values'][0]['contact_id'],
      ),
    );
  }
  else {
    $res = db_query('SELECT name, uid FROM {users} u WHERE status = 1');

    foreach ($res as $record) {
      $dao = CRM_Core_DAO::executeQuery('
        SELECT c.*, org.display_name as organization_name
          FROM civicrm_contact c
          LEFT JOIN civicrm_contact org ON (c.employer_id = org.id)
          LEFT JOIN civicrm_uf_match uf ON (uf.contact_id = c.id)
         WHERE uf_id = %1', array(
        1 => array($record->uid, 'Positive'),
      ));

      if ($dao->fetch()) {
        $email = CRM_Core_DAO::singleValueQuery('SELECT email FROM civicrm_email WHERE contact_id = %1 AND is_primary = 1 LIMIT 1', array(
          1 => array($dao->id, 'Positive'),
        ));

        $result[] = array(
          'uf_name' => $record->name,
          'id' => $dao->id,
          'first_name' => $dao->first_name,
          'last_name' => $dao->last_name,
          'email' => $email,
          'job_title' => $dao->job_title,
          'organization_name' => $dao->organization_name,
        );
      }
    }
  }

  return civicrm_api3_create_success($result, $params, 'Contact', 'getfromuf');
}
