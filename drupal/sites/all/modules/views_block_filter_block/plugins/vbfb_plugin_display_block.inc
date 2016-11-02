<?php

/**
 * @file
 * Contains the overridden block display plugin.
 */

/**
 * The plugin that overrides block display exposed form options.
 *
 * @ingroup views_display_plugins
 */
class vbfb_plugin_display_block extends views_plugin_display_block {

  /**
   * Allows block views to put exposed filter forms in blocks.
   */
  public function uses_exposed_form_in_block() {
    return TRUE;
  }

  /**
   * Block views use exposed widgets only if AJAX is set, or if it has it's
   * exposed form as a block.
   */
  function uses_exposed() {
    if ($this->use_ajax() || !empty($this->display->display_options['exposed_block'])) {
      return views_plugin_display::uses_exposed();
    }
    return FALSE;
  }

}
