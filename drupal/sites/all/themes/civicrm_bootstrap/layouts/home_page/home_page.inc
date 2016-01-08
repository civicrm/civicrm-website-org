<?php

/**
 * Implements hook_panels_layouts().
 */
function civicrm_bootstrap_home_page_panels_layouts() {
  $items['home_page'] = array(
    'title' => t('Home page'),
    'category' => t('CiviCRM'),
    'theme' => 'home_page',
    'icon' => 'home-page.png',
    'regions' => array(
      'banner' => t('Banner'),
      'intro' => t('Intro'),
      'features' => t('Features'),
      'get-started' => t('Get started'),
      'community-highlight' => t('Community highlight'),
      'get-involved' => t('Get involved'),
      'news' => t('News and events'),
      'support-us' => t('Support us'),
      'partners' => t('Partners'),
    ),
  );
  return $items;
}
