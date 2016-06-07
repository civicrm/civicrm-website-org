<?php

/**
 * @file
 * Rules integration for the role expire module.
 */

/**
 * Implementation of hook_rules_action_info().
 */
function role_expire_rules_action_info() {
  $items['role_expire_rules_action_set_role_expire'] = array(
    'label' => t('Set expire time for role'),
    'group' => t('Role Expire'),
    'help' => t('Add an expiration time to a role a user has. This action does not add the role, only the expiration time.'),
    'parameter' => array(
      'user' => array(
        'type' => 'user',
        'label' => t('User'),
        'description' => t('The user which the expiration time will be added'),
      ),
      'role' => array(
        'type' => 'integer',
        'label' => t('Role'),
        'options list' => 'rules_user_roles_options_list',
      ),
      'timestamp' => array(
        'type' => 'date',
        'label' => t('Expiration'),
        'description' => t('The expiration time to be added'),
      ),
    ),
    'module' => 'role_expire',
  );
  $items['role_expire_rules_action_set_role_expire_to_longest'] = array(
    'label' => t('Set expire time to longest for this user'),
    'group' => t('Role Expire'),
    'help' => t('Set an expiration time to the longest attached to a role this user has. This action does not add the role, but only sets the expiration time.'),
    'parameter' => array(
      'user' => array(
        'type' => 'user',
        'label' => t('User'),
        'description' => t('The user for which the expiration time will be set'),
      ),
      'role' => array(
        'type' => 'integer',
        'label' => t('Role'),
        'options list' => 'rules_user_roles_options_list',
      ),
    ),
    'module' => 'role_expire',
  );
  $items['role_expire_rules_action_extend_role_expire'] = array(
    'label' => t('Extend expire time for role'),
    'group' => t('Role Expire'),
    'help' => t('Extend the expiration time for a role a user has. This action does not add the role, it only extends the expiration time. If no time was set, the current time when the action fires is used as a base.'),
    'parameter' => array(
      'user' => array(
        'type' => 'user',
        'label' => t('User'),
        'description' => t('The user which the expiration time will be added'),
      ),
      'role' => array(
        'type' => 'integer',
        'label' => t('Role'),
        'options list' => 'rules_user_roles_options_list',
      ),
      'timestamp' => array(
        'type' => 'date',
        'label' => t('Expiration'),
        'description' => t('The relative amount of time to be added.'),
      ),
    ),
    'module' => 'role_expire',
  );
  $items['role_expire_rules_action_remove_role_expire'] = array(
    'label' => t('Remove expire time from role'),
    'group' => t('Role Expire'),
    'help' => t('Remove an expiration time to a role a user has. This action does not remove the role, only the expiration time.'),
    'parameter' => array(
      'user' => array(
        'type' => 'user',
        'label' => t('User'),
        'description' => t('The user from which the expiration time will be removed'),
      ),
      'role' => array(
        'type' => 'integer',
        'label' => t('Role'),
        'options list' => 'rules_user_roles_options_list',
      ),
    ),
    'module' => 'role_expire',
  );

  return $items;
}

/**
 * Action: Add expire time to role.
 */
function role_expire_rules_action_set_role_expire($account, $rid, $timestamp) {
  role_expire_write_record($account->uid, $rid, $timestamp);
}

/**
 * Action: Set expire time for role to longest attached to this user. Defaults 
 * to the current time.
 */
function role_expire_rules_action_set_role_expire_to_longest($account, $rid, $timestamp) {
  // Set an invalid timestamp to allow for comparisons without warnings.
  $timestamp = -1;
  foreach($account->roles as $id => $name) {
    $this_ts = role_expire_get_user_role_expiry_time($account->uid, $id);    
    if (!empty($this_ts)) {
      $timestamp = ($this_ts > $timestamp) ? $this_ts : $timestamp;
    }
  }
  // If there were no valid timestamps for comparison, use the current time.
  if ($timestamp == -1) {
    $timestamp = time();
  }
  role_expire_write_record($account->uid, $rid, $timestamp);
}

/**
 * Action: Extend expire time for role. Defaults to the current time.
 */
function role_expire_rules_action_extend_role_expire($account, $rid, $extend) {
  $now = time();

  // $extend comes in as relative to now. We just want the difference.
  $diff = $extend - $now;

  $timestamp = role_expire_get_user_role_expiry_time($account->uid, $rid);
  $base = (!empty($timestamp)) ? $timestamp : time();
  $new_ts = $base + $diff;

  role_expire_write_record($account->uid, $rid, $new_ts);
}

/**
 * Action: Remove expire time from role
 */
function role_expire_rules_action_remove_role_expire($account, $rid) {
  role_expire_delete_record($account->uid, $rid);
}
