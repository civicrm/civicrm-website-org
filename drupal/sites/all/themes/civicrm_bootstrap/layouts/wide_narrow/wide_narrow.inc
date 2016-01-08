<?php

/**
 * Implements hook_panels_layouts().
 */
function civicrm_bootstrap_wide_narrow_panels_layouts() {
  $items['wide_narrow'] = array(
    'title' => t('Double (wide then narrow)'),
    'category' => t('CiviCRM'),
    'theme' => 'wide_narrow',
    'icon' => 'wide-narrow.png',
    'regions' => array(
      'left' => t('Left'),
      'right' => t('Right'),
    ),
  );
  return $items;
}
