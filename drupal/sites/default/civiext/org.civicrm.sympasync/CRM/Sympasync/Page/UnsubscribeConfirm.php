<?php

class CRM_Sympasync_Page_UnsubscribeConfirm extends CRM_Core_Page {

  public function run() {
    CRM_Utils_System::setTitle(ts('Unsubscribe'));

    $values = CRM_Sympasync_Signer::validateConfirmation($_GET);
    $this->assign('confirmed', !empty($values));

    if (!empty($values)) {
      $this->assign('email', $values['email']);
      $this->assign('group', CRM_Contact_DAO_Group::findById($values['gid']));
      $contacts = CRM_Core_DAO::executeQuery('SELECT DISTINCT contact_id FROM civicrm_email WHERE email = %1', array(
        1 => array($values['email'], 'String'),
      ))->fetchAll();
      $contactIds = array_filter(CRM_Utils_Array::collect('contact_id', $contacts), 'is_numeric');
      if (!empty($contactIds)) {
        list($total, $removed, $notremoved) = CRM_Contact_BAO_GroupContact::removeContactsFromGroup(
          $contactIds, $values['gid'], 'Email');
      }
    }

    parent::run();
  }

}
