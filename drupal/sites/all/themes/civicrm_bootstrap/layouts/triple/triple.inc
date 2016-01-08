<?php

/**
 * Implements hook_panels_layouts().
 */
function civicrm_bootstrap_triple_panels_layouts() {
  $items['triple'] = array(
    'title' => t('Triple'),
    'category' => t('CiviCRM'),
    'theme' => 'triple',
    'icon' => 'triple.png',
    'regions' => array(
      'left' => t('Left'),
      'middle' => t('Middle'),
      'right' => t('Right'),
    ),
  );
  return $items;
}
