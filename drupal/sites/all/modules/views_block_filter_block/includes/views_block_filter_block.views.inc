<?php

/**
 * @file
 * Views hooks and functions for the Views Block Filter Block module.
 */


/**
 * Implements hook_views_plugins_alter().
 */
function views_block_filter_block_views_plugins_alter(&$plugins) {
  // Force the block display plugin to use our block display plugin.
  $plugins['display']['block']['handler'] = 'vbfb_plugin_display_block';
  $plugins['display']['block']['path'] = drupal_get_path('module', 'views_block_filter_block') . '/plugins';
  $plugins['display']['block']['file'] = 'vbfb_plugin_display_block.inc';
}
