<?php

class CRM_CivirulesConditions_Utils_GroupContact {

  /**
   * Checks wether a contact is a member of a group
   *
   * This function is a copy of CRM_Contact_BAO_GroupContact::isContactInGroup but with
   * a change so that the group contact cache won't be rebuild. Which somehow resulted
   * in a deadlock
   *
   * @param $contact_id
   * @param $group_id
   * @return bool
   */
  public static function isContactInGroup($contact_id, $group_id) {
    if (!CRM_Utils_Rule::positiveInteger($contact_id) ||
      !CRM_Utils_Rule::positiveInteger($group_id)
    ) {
      return FALSE;
    }

    $params = array(
      array('group', 'IN', array($group_id => 1), 0, 0),
      array('contact_id', '=', $contact_id, 0, 0),
    );
    list($contacts, $_) = CRM_Contact_BAO_Query::apiQuery($params, array('contact_id'), null, null, 0, 1, false, false, true);

    if (!empty($contacts)) {
      return TRUE;
    }
    return FALSE;
  }

}