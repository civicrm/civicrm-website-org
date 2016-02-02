<?php

require_once 'civirules.civix.php';
if (!interface_exists("\\Psr\\Log\\LoggerInterface")) {
  require_once('psr/log/LoggerInterface.php');
}
if (!class_exists("\\Psr\\Log\\LogLevel")) {
  require_once('psr/log/LogLevel.php');
}

/**
 * Implementation of hook_civicrm_config
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function civirules_civicrm_config(&$config) {
  _civirules_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function civirules_civicrm_xmlMenu(&$files) {
  _civirules_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function civirules_civicrm_install() {
  return _civirules_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function civirules_civicrm_uninstall() {
  return _civirules_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function civirules_civicrm_enable() {
  return _civirules_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function civirules_civicrm_disable() {
  return _civirules_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function civirules_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _civirules_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function civirules_civicrm_managed(&$entities) {
  return _civirules_civix_civicrm_managed($entities);
}

/**
 * Implementation of hook_civicrm_caseTypes
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function civirules_civicrm_caseTypes(&$caseTypes) {
  _civirules_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implementation of hook_civicrm_alterSettingsFolders
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function civirules_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _civirules_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implementation of hook civicrm_navigationMenu
 * to create a CiviRules menu item in the Administer menu
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 */
function civirules_civicrm_navigationMenu( &$params ) {
    $item = array (
        'name'          =>  ts('CiviRules'),
        'url'           =>  CRM_Utils_System::url('civicrm/civirules/page/rule', 'reset=1', true),
        'permission'    => 'administer CiviCRM',
    );
    _civirules_civix_insert_navigation_menu($params, 'Administer', $item);
}

function civirules_civicrm_pre($op, $objectName, $objectId, &$params) {
  CRM_Civirules_Utils_PreData::pre($op, $objectName, $objectId, $params);
  CRM_Civirules_Utils_CustomDataFromPre::pre($op, $objectName, $objectId, $params);
}

function civirules_civicrm_post( $op, $objectName, $objectId, &$objectRef ) {
  CRM_Civirules_Trigger_Post::post($op, $objectName, $objectId, $objectRef);
}

function civirules_civirules_alter_trigger_data(CRM_Civirules_TriggerData_TriggerData &$triggerData) {
  //also add the custom data which is passed to the pre hook (and not the post)
  CRM_Civirules_Utils_CustomDataFromPre::addCustomDataToTriggerData($triggerData);
}