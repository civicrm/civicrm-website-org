<?php

/**
 * Implements hook_panels_layouts().
 */
function civicrm_bootstrap_stripes_with_banner_panels_layouts() {
  $items['stripes_with_banner'] = array(
    'title' => t('Stripes with a photo banner'),
    'category' => t('CiviCRM'),
    'theme' => 'stripes',
    'icon' => 'stripes-with-banner.png',
    'regions' => array(
      'banner' => t('Banner'),
      'white' => t('White'),
      'blue' => t('Blue'),
      'extra-white' => t('Second white'),
      'green' => t('Green'),
    ),
  );
  return $items;
}
