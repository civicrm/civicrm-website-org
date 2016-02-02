<?php
/**
 * Class following Singleton pattern for specific extension configuration
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @license AGPL-3.0
 */
class CRM_Civirules_Config
{
  /*
   * singleton pattern
   */
  static private $_singleton = NULL;
  /*
   * properties to hold the valid entities and actions for civirule trigger
   */
  protected $validTriggerObjectNames = NULL;
  protected $validTriggerOperations = NULL;

  /**
   * Constructor
   */
  function __construct()
  {
    $this->setTriggerProperties();
  }

  /**
   * Function to return singleton object
   *
   * @return object $_singleton
   * @access public
   * @static
   */
  public static function &singleton()
  {
    if (self::$_singleton === NULL) {
      self::$_singleton = new CRM_Civirules_Config();
    }
    return self::$_singleton;
  }

  /**
   * Function to get the valid trigger entities
   *
   * @return int
   * @access public
   */
  public function getValidTriggerObjectNames()
  {
    return $this->validTriggerObjectNames;
  }

  /**
   * Function to get the valid trigger actions
   *
   * @return int
   * @access public
   */
  public function getValidTriggerOperations()
  {
    return $this->validTriggerOperations;
  }
  protected function setTriggerProperties() {
    $this->validTriggerOperations = array(
      'create',
      'edit',
      'delete',
      'restore',
      'trash');

    $this->validTriggerObjectNames = array(
      'Activity',
      'Address',
      'Case',
      'Contact',
      'Contribution',
      'ContributionRecur',
      'Email',
      'EntityTag',
      'Event',
      'Grant',
      'Group',
      'GroupContact',
      'Household',
      'Individual',
      'Membership',
      'MembershipPayment',
      'Organization',
      'Participant',
      'ParticipantPayment',
      'Phone',
      'Pledge',
      'PledgePayment',
      'Relationship',
      'Tag');
  }
}