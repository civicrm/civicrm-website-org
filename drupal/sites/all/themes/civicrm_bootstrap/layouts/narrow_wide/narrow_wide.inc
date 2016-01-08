<?php

/**
 * Implements hook_panels_layouts().
 */
function civicrm_bootstrap_narrow_wide_panels_layouts()
{
    $items['narrow_wide'] = array(
    'title' => t('Double (narrow then wide)'),
    'category' => t('CiviCRM'),
    'theme' => 'narrow_wide',
    'icon' => 'narrow-wide.png',
    'regions' => array(
      'left' => t('Left'),
      'right' => t('Right'),
    ),
  );

    return $items;
}
