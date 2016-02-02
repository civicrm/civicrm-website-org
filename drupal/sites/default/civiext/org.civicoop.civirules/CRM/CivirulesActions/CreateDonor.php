<?php
/**
 * Class for CiviRules Create Donor (set contact subtype Donor for Contact)  Action
 *
 * @author Erik Hommel (CiviCooP) <erik.hommel@civicoop.org>
 * @license AGPL-3.0
 */
class CRM_CivirulesActions_CreateDonor extends CRM_Civirules_Action {
  /**
   * Method processAction to execute the action
   *
   * @param CRM_Civirules_TriggerData_TriggerData $triggerData
   * @access public
   *
   */
  public function processAction(CRM_Civirules_TriggerData_TriggerData $triggerData) {
    $contactId = $triggerData->getContactId();
    $processContact = false;

    // retrieve contact type of contact
    $contactParams = array('id' => $contactId, 'return' => 'contact_type');
    $contactType = civicrm_api3('Contact', 'Getvalue', $contactParams);
    // retrieve contact type Donor and only execute if the same
    $donorType = civicrm_api3('ContactType', 'Getsingle', array('name' => 'Donor'));
    try {
      switch ($contactType) {
        case 'Individual':
          if ($donorType['parent_id'] = 1) {
            $processContact = true;
          }
        break;
        case 'Household':
          if ($donorType['parent_id'] = 2) {
            $processContact = true;
          }
          break;
        case 'Organization':
          if ($donorType['parent_id'] = 3) {
            $processContact = true;
          }
          break;
      }
    } catch (CiviCRM_API3_Exception $ex) {
    }
    if ($processContact) {
      $newParams = array('id' => $contactId, 'contact_sub_type' => 'Donor');
      civicrm_api3('Contact', 'Create', $newParams);
    }
  }
  /**
   * Method to return the url for additional form processing for action
   * and return false if none is needed
   *
   * @param int $ruleActionId
   * @return bool
   * @access public
   */
  public function getExtraDataInputUrl($ruleActionId) {
    return FALSE;
  }


}