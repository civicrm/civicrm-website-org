<?php

require_once 'ldapauthapi.civix.php';

/**
 * Implementation of hook_civicrm_config
 */
function ldapauthapi_civicrm_config(&$config) {
  _ldapauthapi_civix_civicrm_config($config);
}
