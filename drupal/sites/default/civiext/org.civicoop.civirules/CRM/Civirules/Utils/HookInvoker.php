<?php

/**
 * Class CRM_Civirules_Utils_HookInvoker
 *
 * This class invokes hooks through the civicrm core hook invoker functionality
 */
class CRM_Civirules_Utils_HookInvoker {

  private static $singleton;

  private function __construct() {

  }

  public static function singleton() {
    if (!self::$singleton) {
      self::$singleton = new CRM_Civirules_Utils_HookInvoker();
    }
    return self::$singleton;
  }

  /**
   * hook_civicrm_civirules_logger
   *
   * @param \Psr\Log\LoggerInterface|NULL $logger
   *
   * This hook could set a logger class for Civirules
   */
  public function hook_civirules_getlogger(&$logger = null) {
    $this->invoke('civirules_logger', 1, $logger);
    if ($logger && !$logger instanceof \Psr\Log\LoggerInterface) {
      $logger = null;
    }
  }

  public function hook_civirules_alterTriggerData(CRM_Civirules_TriggerData_TriggerData &$triggerData) {
    $this->invoke('civirules_alter_trigger_data', 1, $triggerData);
  }

  private function invoke($fnSuffix, $numParams, &$arg1 = null, &$arg2 = null, &$arg3 = null, &$arg4 = null, &$arg5 = null) {
    $hook =  CRM_Utils_Hook::singleton();
    $civiVersion = CRM_Core_BAO_Domain::version();

    if (version_compare($civiVersion, '4.5', '<')) {
      //in CiviCRM 4.4 the invoke function has 5 arguments maximum
      return $hook->invoke($numParams, $arg1, $arg2, $arg3, $arg4, $arg5, $fnSuffix);
    } else {
      //in CiviCRM 4.5 and later the invoke function has 6 arguments
      return $hook->invoke($numParams, $arg1, $arg2, $arg3, $arg4, $arg5, CRM_Utils_Hook::$_nullObject, $fnSuffix);
    }
  }

}