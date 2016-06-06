<?php

/**
 * Implementation of hook_civicrm_config
 */
function statistics_civicrm_config(&$config) {
  $extRoot = dirname( __FILE__ ) . DIRECTORY_SEPARATOR;
  set_include_path($extRoot . PATH_SEPARATOR . get_include_path());
}