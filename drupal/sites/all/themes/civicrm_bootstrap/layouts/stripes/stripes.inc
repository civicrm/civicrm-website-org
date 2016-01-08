<?php

/**
 * Implements hook_panels_layouts().
 */
function civicrm_bootstrap_stripes_panels_layouts() {
  $items['stripes'] = array(
    'title' => t('Stripes'),
    'category' => t('CiviCRM'),
    'theme' => 'home_page',
    'icon' => 'stripes.png',
    'regions' => array(
      'white' => t('White'),
      'blue' => t('Blue'),
      'extra-white' => t('Second white'),
      'green' => t('Green'),
    ),
  );
  return $items;
}
