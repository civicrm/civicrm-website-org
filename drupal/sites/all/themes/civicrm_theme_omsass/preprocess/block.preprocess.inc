<?php

function civicrm_theme_omsass_preprocess_block(&$variables) {
	if ($variables['elements']['#block']->delta == 'homepage_news-block') {
		$views = views_get_view_result('homepage_news','block');
		$fid = $views[0]->field_field_image[0]['rendered']['#item']['fid'];
		$file = file_load($fid);
		$uri = $file->uri;
		$variables['elements']['#block']->backgroundImage = file_create_url($uri);

		// var_dump($variables['elements']);
	}
}