<?php 

function civicrm_theme_omsass_preprocess_page(&$variables) {
	switch($variables['node']->type) {
		case 'sectioned_page':
			// var_dump($variables['node']->field_image);
			$imageUrl = file_create_url($variables['node']->field_image['und'][0]['uri']);
			$variables['title'] = '';
			break;	
	}

	// Add icon to title
	if (isset($variables['node']) && !empty($variables['node']->field_icon) && !empty($variables['node']->field_icon['und'][0]['uri']) && !arg(2)) {
		$icon = '<img src="' . file_create_url($variables['node']->field_icon['und'][0]['uri']) . '" />';
		$variables['title'] = $icon . $variables['title'];
	}
}
