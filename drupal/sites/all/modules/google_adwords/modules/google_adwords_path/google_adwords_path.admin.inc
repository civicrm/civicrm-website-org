<?php

/**
 * @file
 * Admin page for Google Adwords Path.
 */

/**
 * Admin page listing down all Google Adwords Path conversion codes.
 */
function google_adwords_path_admin_page() {
  $codes = google_adwords_path_get_all_codes();

  $header = array(
    t('No'),
    t('Name'),
    t('Conversion ID'),
    t('Language'),
    t('Format'),
    t('Color'),
    t('Label'),
    t('Operations'),
  );
  $rows = array();
  $count = 1;
  foreach ($codes as $code) {
    $rows[] = array(
      array('data' => $count),
      array('data' => $code['name']),
      array('data' => $code['conversion_id']),
      array('data' => $code['conversion_language']),
      array('data' => $code['conversion_format']),
      array('data' => $code['conversion_color']),
      array('data' => $code['conversion_label']),
      array('data' => l(t('edit'), 'admin/config/system/google_adwords/path/' . $code['cid'] . '/edit') . ' ' . l(t('delete'), 'admin/config/system/google_adwords/path/' . $code['cid'] . '/delete')),
    );
    $count++;
  }

  if ($rows) {
    return theme('table', array('header' => $header, 'rows' => $rows));
  }
  else {
    return '<p>' . t('No conversion code yet. !add_link?', array('!add_link' => l(t('Add one'), 'admin/config/system/google_adwords/path/add'))) . '</p>';
  }
}

/**
 * Conversion code add / edit form.
 */
function google_adwords_path_code_form($form, $form_state, $cid = NULL) {
  global $language;
  $form = array();

  // If updating existing code, add the conversion id to the form.
  if ($cid) {
    $code = google_adwords_path_load_code_by_cid($cid);
    $form['cid'] = array(
      '#type' => 'value',
      '#value' => $cid,
    );
  }

  $form['name'] = array(
    '#type' => 'textfield',
    '#title' => t('Name'),
    '#default_value' => isset($code) ? $code['name'] : '',
    '#size' => 75,
    '#maxlength' => 64,
    '#required' => TRUE,
    '#description' => t('The name of this conversion code. This will appear in the administrative interface to easily identify it.'),
  );
  $form['conversion_id'] = array(
    '#type' => 'textfield',
    '#title' => t('Conversion ID'),
    '#default_value' => isset($code) ? $code['conversion_id'] : '',
    '#size' => 15,
    '#maxlength' => 64,
    '#required' => TRUE,
  );
  $form['conversion_language'] = array(
    '#type' => 'textfield',
    '#title' => t('Conversion Language'),
    '#default_value' => isset($code) ? $code['conversion_language'] : variable_get('google_adwords_conversion_language', $language->language),
    '#size' => 15,
    '#maxlength' => 64,
    '#required' => TRUE,
  );
  $form['conversion_format'] = array(
    '#type' => 'textfield',
    '#title' => t('Conversion Format'),
    '#default_value' => isset($code) ? $code['conversion_format'] : variable_get('google_adwords_conversion_format', '2'),
    '#size' => 15,
    '#maxlength' => 64,
    '#required' => TRUE,
  );
  $form['conversion_color'] = array(
    '#type' => 'textfield',
    '#title' => t('Conversion Color'),
    '#default_value' => isset($code) ? $code['conversion_color'] : variable_get('google_adwords_conversion_color', 'FFFFFF'),
    '#size' => 15,
    '#maxlength' => 64,
    '#required' => TRUE,
  );
  $form['conversion_label'] = array(
    '#type' => 'textfield',
    '#title' => t('Conversion Label'),
    '#default_value' => isset($code) ? $code['conversion_label'] : '',
    '#size' => 30,
    '#maxlength' => 64,
    '#required' => TRUE,
  );
  $form['paths'] = array(
    '#type' => 'textarea',
    '#title' => t('Paths'),
    '#default_value' => isset($code) ? $code['paths'] : '',
    '#rows' => 8,
    '#cols' => 128,
    '#required' => TRUE,
    '#description' => t('A list of paths, separated by a new line, where this conversion code should be inserted.'),
  );
  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Save'),
  );

  if ($cid) {
    $form['delete'] = array(
      '#type' => 'submit',
      '#value' => t('Delete'),
    );
  }

  return $form;
}

/**
 * Submit handler for conversion code add or edit form.
 */
function google_adwords_path_code_form_submit($form, &$form_state) {
  if ($form_state['clicked_button']['#value'] == 'Save') {
    form_state_values_clean($form_state);

    google_adwords_path_save_code($form_state['values']);
    // Updating an existing conversion code.
    if (isset($form_state['values']['cid'])) {
      $message = 'Successfully updated %name.';
    }
    // Adding a new conversion code.
    else {
      $message = 'Successfully added %name.';
    }

    drupal_set_message(t($message, array('%name' => $form_state['values']['name'])));

    // Redirect back to Google Adwords Path admin page.
    drupal_goto('admin/config/system/google_adwords/path');
  }
  elseif ($form_state['clicked_button']['#value'] == 'Delete') {
    drupal_goto('admin/config/system/google_adwords/path/' . $form_state['values']['cid'] . '/delete');
  }
}

/**
 * Confirmation form before deleting conversion code.
 */
function google_adwords_path_code_delete_form($form, &$form_state, $cid) {
  $form = array();
  $path = 'admin/config/system/google_adwords/path';

  $code = google_adwords_path_load_code_by_cid($cid);
  $form['cid'] = array(
    '#type' => 'value',
    '#value' => $code['cid'],
  );
  $form['name'] = array(
    '#type' => 'value',
    '#value' => $code['name'],
  );

  return confirm_form($form, t('Are you sure you want to delete %name?', array('%name' => $code['name'])), $path);
}

/**
 * Implements hook_form_submit().
 */
function google_adwords_path_code_delete_form_submit($form, &$form_state) {
  if (google_adwords_path_delete_code_by_cid($form_state['values']['cid'])) {
    $message = 'Successfully deleted %name.';
  }
  else {
    $message = 'There was a problem deleting Google Adwords Conversion code, %name.';
  }

  drupal_set_message(t($message, array('%name' => $form_state['values']['name'])));


  drupal_goto('admin/config/system/google_adwords/path');
}
