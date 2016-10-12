<?php
/**
 * @file views_import.views_default.inc
 */

/**
 * Includes file with view and returns view object or NULL if file don't have a
 * view.
 *
 * @param string $filename
 *
 * @return mixed
 */
function _views_import_fetch_view($uri) {
  $view = NULL;
  include $uri;
  if ($view && isset($view->name)) {
    return $view;
  }
  else {
    return NULL;
  }
}

/**
 * Implements hook_views_default_views().
 *
 * @return array
 */
function views_import_views_default_views() {
  $files = file_scan_directory(VIEWS_IMPORT_DIR, '/\.inc$/', array('recurse' => FALSE));

  $views = array();
  foreach ($files as $file) {
    $view = _views_import_fetch_view($file->uri);
    if ($view) {
      $view->tag = 'views_import';
      $views[$view->name] = $view;
    }
  }

  return $views;
}
