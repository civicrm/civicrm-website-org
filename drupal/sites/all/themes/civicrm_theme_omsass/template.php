<?php

/**
 * @file
 * Template overrides as well as (pre-)process and alter hooks for the
 * CiviCRM-o4 theme.
 */


/**
 * Default theme implementation for flexslider_list
 */
function civicrm_theme_omsass_flexslider_list(&$vars) {
  // Reference configuration variables
  $optionset = &$vars['settings']['optionset'];
  $items = &$vars['items'];
  $attributes = &$vars['settings']['attributes'];
  $type = &$vars['settings']['type'];
  $output = '';
  $view = views_get_view_result('homepage_slider','block');

  // Build the list
  if (!empty($items)) {
    $output .= "<$type" . drupal_attributes($attributes) . '>';

    foreach ($items as $i => $item) {  	
    	// Get the uri of the image
    	$fid = $view[$i]->field_field_banner_image[0]['rendered']['#item']['fid'];
		$file = file_load($fid);
		$uri = $file->uri;

      $caption = '';
      if (!empty($item['caption'])) {
        $caption = $item['caption'];
      }

      $output .= theme('flexslider_list_item', array(
        'backgroundImage' => file_create_url($uri),
        'item' => $item['slide'],
        'settings' => array(
          'optionset' => $optionset,
        ),
        'caption' => $caption,
      ));
    }
    $output .= "</$type>";
  }

  return $output;
}

/**
 * Default theme implementation for flexslider_list_item
 */
function civicrm_theme_omsass_flexslider_list_item(&$vars) {
  $listItem = sprintf("<li %s style=\"background: url('%s') no-repeat center center; background-size: cover;\"> %s %s</li>\n",drupal_attributes($vars['settings']['attributes']),$vars['backgroundImage'],$vars['item'],$vars['caption']);
  return $listItem;
}


/**
 * Theme hook for the header block
 */

function civicrm_theme_omsass_theme($existing, $type, $theme, $path) {
  return array(
    'header' => array(
      'template' => 'sites/all/themes/civicrm_theme_omsass/templates/header', // without the .tpl.php extension
      'variables' => array(), // to define default values for passed variables
     )
  );
}