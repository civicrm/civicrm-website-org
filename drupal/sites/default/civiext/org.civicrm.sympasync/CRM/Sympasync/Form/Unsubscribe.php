<?php

/**
 * CiviCRM-Sympa Unsubscribe Form.
 *
 * This is a general unsubscribe form that works with any mailing list. General pageflow:
 *
 * 1. User receives an email from the mailing-list with an unsubscribe link
 *    (eg 'http://example.com/civicrm/sympa/unsubscribe?gid=123&email=foo@example.com').
 * 2. User clicks link, views CRM_Sympasync_Form_Unsubscribe, and confirms.
 * 3. CRM_Sympasync_Form_Unsubscribe sends an email confirmation.
 * 4. User receives email with another link
 *   (eg 'http://example.com/civicrm/sympa/unsubscribe/confirm?code=abcd1234abcd1234').
 * 5. User clicks link and views CRM_Sympasync_Page_UnsubscribeConfirm.
 */
class CRM_Sympasync_Form_Unsubscribe extends CRM_Core_Form {

  const MAX_EMAIL_LEN = 200;

  public function setDefaultValues() {
    $result = array();

    $email = CRM_Utils_Request::retrieve('email', 'String');
    if (strlen($email) < self::MAX_EMAIL_LEN && filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $result['email'] = $email;
    }

    $result['gid'] = CRM_Utils_Request::retrieve('gid', 'Positive', CRM_Core_DAO::$_nullObject, TRUE);
    if (!$this->isMailingList($result['gid'])) {
      CRM_Core_Error::fatal(ts('Group ID does not refer to a mailing list.'));
    }

    return $result;
  }

  public function buildQuickForm() {
    $defaults = $this->setDefaultValues();

    // add form elements
    $this->add(
      'text', // field type
      'email', // field name
      'Email Address', // field label
      '', // list of options
      TRUE // is required
    );
    $this->addFormRule(array(__CLASS__, 'formRule'), $this);
    $this->add('hidden', 'gid');
    $this->assign('group', CRM_Contact_DAO_Group::findById($defaults['gid']));
    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => ts('Submit'),
        'isDefault' => TRUE,
      ),
    ));

    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  public static function formRule($fields, $files, $self) {
    $errors = array();

    if (!(strlen($fields['email']) < self::MAX_EMAIL_LEN && filter_var($fields['email'], FILTER_VALIDATE_EMAIL))) {
      $errors['email'] = ts('Malformed email');
    }

    return empty($errors) ? TRUE : $errors;
  }

  public function postProcess() {
    $values = $this->exportValues();

    if (!$this->isMailingList($values['gid'])) {
      CRM_Core_Error::fatal(ts('Group ID does not refer to a mailing list.'));
    }

    CRM_Sympasync_Mail::sendUnsubscribeConfirmation($values['gid'], $values['email']);

    $this->assign('sent', 1);
    $this->assign('email', $values['email']);

    parent::postProcess();
  }

  /**
   * Determine whether $gid points to a group for which we can opt-out.
   *
   * @param int $gid
   * @return bool
   */
  public function isMailingList($gid) {
    /** @var CRM_Contact_DAO_Group $group */
    $group = CRM_Contact_DAO_Group::findById($gid);
    $groupTypes = CRM_Utils_Array::explodePadded($group->group_type);
    $mailingListType = 2;
    return in_array($mailingListType, $groupTypes);
  }

  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  public function getRenderableElementNames() {
    // The _elements list includes some items which should not be
    // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
    // items don't have labels.  We'll identify renderable by filtering on
    // the 'label'.
    $elementNames = array();
    foreach ($this->_elements as $element) {
      /** @var HTML_QuickForm_Element $element */
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }

}
