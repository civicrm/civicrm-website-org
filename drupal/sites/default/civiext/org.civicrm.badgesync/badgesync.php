<?php

require_once 'badgesync.civix.php';

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function badgesync_civicrm_config(&$config) {
  _badgesync_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @param array $files
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function badgesync_civicrm_xmlMenu(&$files) {
  _badgesync_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function badgesync_civicrm_install() {
  _badgesync_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function badgesync_civicrm_uninstall() {
  _badgesync_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function badgesync_civicrm_enable() {
  _badgesync_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function badgesync_civicrm_disable() {
  _badgesync_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed
 *   Based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function badgesync_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _badgesync_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function badgesync_civicrm_managed(&$entities) {
  _badgesync_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * @param array $caseTypes
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function badgesync_civicrm_caseTypes(&$caseTypes) {
  _badgesync_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function badgesync_civicrm_angularModules(&$angularModules) {
_badgesync_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function badgesync_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _badgesync_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Functions below this ship commented out. Uncomment as required.
 *

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function badgesync_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
function badgesync_civicrm_navigationMenu(&$menu) {
  _badgesync_civix_insert_navigation_menu($menu, NULL, array(
    'label' => ts('The Page', array('domain' => 'org.civicrm.badgesync')),
    'name' => 'the_page',
    'url' => 'civicrm/the-page',
    'permission' => 'access CiviReport,access CiviContribute',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _badgesync_civix_navigationMenu($menu);
} // */

/**
 * This hook is used to update contributor badges for Partners, Members and Sponsors
 * based on the groups. Badge information is store in the custom data: Service Provider - Badges
 *
 * Logic: For Partners, Members, Sponsors
 * a. Group “Contributors / Supporting Contributors” -> display civibadge-contributor-supporting.png
 * b. Group “Contributors / Empowering Contributors” -> display civibadge-contributor-empowering.png
 * c. Group “Contributors / Sustaining Contributors” -> display civibadge-contributor-sustaining.png
 *
 * @param $op string operation create, edit, remove, etc
 * @param $objectName string name of the object
 * @param $groupId int object id such as group id
 * @param $contactIds array reference to the object, in our case contact ids
 */
function badgesync_civicrm_post($op, $objectName, $groupId, $contactIds) {
  // step 1: trap when the contact is added to the group
  // group id's specific to groups that we are interested.
  // 488 supporting, 489 - enpowering, 490 sustaining
  $groupIds = array(488, 489, 490);

  if ($objectName == 'GroupContact' && in_array($groupId, $groupIds) && !empty($contactIds)) {
    // get the badge image based on the group
    // image format follows the pattern: civibadge-contributor-supporting.png
    switch ($groupId) {
      case 488:
        $badgeImage = 'contributor-supporting';
        break;
      case 489:
        $badgeImage = 'contributor-empowering';
        break;
      case 490:
        $badgeImage = 'contributor-sustaining';
        break;
    }

    // step 2: Fetch the existing value custom data: Service Provide - Badges
    // loop through contacts that are updated and fetch the existing value for badge
    // custom_160 is the custom field id for badges
    foreach ($contactIds as $contactId) {
      $result = civicrm_api3('Contact', 'get', array(
        'sequential' => 1,
        'return' => array("custom_160"),
        'id' => $contactId,
      ));

      if (!empty($result['values'][0])) {
        $customFieldValue = json_decode($result['values'][0]['custom_160']);

        if ($op == 'create') {
          // add the badge value to the array if does not exist
          if (!in_array($badgeImage, $customFieldValue)) {
            $customFieldValue[] = $badgeImage;
          }
        }
        else {
          if ($op == 'delete') {
            // remove the badge value from the array
            if (($key = array_search($badgeImage, $customFieldValue)) !== FALSE) {
              unset($customFieldValue[$key]);
            }

          }
        }
      }

      // step 3: Update the badges for the contact
      // add quotes to array elements
      // Note to self: sometime in the near / far future come back to this code and make below code more classy :)
      // instead of old style foreach, may be use arrar_map()
      if (!empty($customFieldValue)) {
        foreach ($customFieldValue as $index => $val) {
          $customFieldValue[$index] = '"'. $val .'"';
        }
      }

      $updatedBadgeValue = '[' . implode(',', $customFieldValue) . ']';

      // update custom field value
      civicrm_api3('Contact', 'create', array(
        'sequential' => 1,
        'id' => $contactId,
        'custom_160' => $updatedBadgeValue,
      ));

    }
  }

}
