<?php 

function civicrm_theme_omsass_preprocess_page(&$variables) {
	switch($variables['node']->type) {
		case 'sectioned_page':
			// var_dump($variables['node']->field_image);
			$imageUrl = file_create_url($variables['node']->field_image['und'][0]['uri']);
			$variables['title'] = '';
			break;	
	}	
}