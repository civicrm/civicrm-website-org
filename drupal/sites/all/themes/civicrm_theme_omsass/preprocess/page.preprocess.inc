<?php

function civicrm_theme_omsass_preprocess_page(&$variables) {
  if (isset($variables['node'])) {
    switch ($variables['node']->type) {
      case 'sectioned_page':
        // $imageUrl = file_create_url($variables['node']->field_image['und'][0]['uri']);
        $variables['title'] = '';
        break;
    }

    // Add icon to title
    if (!empty($variables['node']->field_icon) && !empty($variables['node']->field_icon['und'][0]['uri']) && !arg(2)) {
      $icon = '<img src="' . file_create_url($variables['node']->field_icon['und'][0]['uri']) . '" />';
      $variables['title'] = $icon . $variables['title'];
    }
  }
}
