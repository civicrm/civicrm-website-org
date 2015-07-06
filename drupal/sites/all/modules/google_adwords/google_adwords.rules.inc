<?php

/**
 * @file
 * Rules integration for google adwords conversion tracking.
 */

/**
 * Implements hook_rules_action_info().
 *
 * Use this rule to add google adword conversion tracking on non entity pages.
 */
function google_adwords_rules_action_info() {
  $actions = array();

  $actions['google_adwords_add_tracking'] = array(
    'label' => t('Set Google Adwords conversion tracking code'),
    'parameter' => array(
      'conversion_id' => array(
        'type' => 'integer',
        'label' => t('Conversion ID'),
        'optional' => FALSE,
      ),
      'label' => array(
        'type' => 'text',
        'label' => t('Label'),
        'optional' => FALSE,
      ),
      'value' => array(
        'type' => 'text',
        'label' => t('Conversion Value'),
        'optional' => TRUE,
        'default value' => 0,
      ),
      'language' => array(
        'type' => 'text',
        'label' => t('Language'),
        'optional' => TRUE,
        'default value' => 'en',
      ),
      'color' => array(
        'type' => 'text',
        'label' => t('Color'),
        'optional' => TRUE,
        'default value' => 'FFFFFF',
      ),
      'format' => array(
        'type' => 'text',
        'label' => t('Format'),
        'optional' => TRUE,
        'default value' => '1',
      ),
    ),
    'group' => t('Google Adwords'),
  );
  return $actions;
}
