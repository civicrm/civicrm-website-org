<?php
/**
 * @file
 * Drush commands for views_import module.
 *
 * Command to export includes files to store Views definitions on the disk.
 */

module_load_include('inc', 'views_import', 'views_import.drush');

/**
 * Callback for views_import_export_views command.
 *
 * @param string $view_name
 *   String containing the name of the view to export.
 */
function drush_views_import_export_views($view_name = '') {
  $views = views_get_all_views();

  if (empty($view_name) && !drush_get_option('all')) {
    return drush_set_error(dt('No view name given, nothing to do :('));
  }

  $to_export = drush_get_option('all')
    ? array_keys($views)
    : array($view_name);

  foreach ($to_export as $view_name) {
    if (!array_key_exists($view_name, $views)) {
      return drush_set_error(dt('"@view_name" is not a valid view name', array(
        '@view_name' => $view_name,
      )));
    }

    _views_import_export_view($views[$view_name]);

    drush_log(dt('The view "@view_name" has been exported to "@path".', array(
      '@view_name' => $view_name,
      '@path' => VIEWS_IMPORT_DIR . '/' . $view_name . '.inc',
    )), 'ok');
  }
}

/**
 * Helper function to write individual views files.
 *
 * @param object $view
 *   The view object to export.
 */
function _views_import_export_view($view) {
  $file_name = $view->name . '.inc';
  $export = "<?php \n" . views_export_view($view);
  file_put_contents(VIEWS_IMPORT_DIR . '/' . $file_name, $export);
}
