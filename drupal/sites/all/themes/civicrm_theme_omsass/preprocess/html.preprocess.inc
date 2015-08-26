<?php

// Load Google Fonts
function civicrm_theme_omsass_preprocess_html(&$variables) {
    drupal_add_css('https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,700', array('type' => 'external'));
}