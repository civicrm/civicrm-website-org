<?php

/**
 * Implements hook_preprocess_page().
 */
function civicrm_o4_preprocess_page(&$variables) {
  // You can use preprocess hooks to modify the variables before they are passed
  // to the theme function or template file.
  
  drupal_add_css('https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,700', array('type' => 'external'));
}
