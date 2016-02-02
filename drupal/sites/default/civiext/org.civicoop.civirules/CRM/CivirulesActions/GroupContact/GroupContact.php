<?php
/**
 * Class for CiviRules Group Contact Action
 *
 * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
 * @license AGPL-3.0
 */

abstract class CRM_CivirulesActions_GroupContact_GroupContact extends CRM_CivirulesActions_Generic_Api {

  /**
   * Returns an array with parameters used for processing an action
   *
   * @param array $params
   * @param object CRM_Civirules_TriggerData_TriggerData $triggerData
   * @return array $params
   * @access protected
   */
  protected function alterApiParameters($params, CRM_Civirules_TriggerData_TriggerData $triggerData) {
    //this function could be overridden in subclasses to alter parameters to meet certain criteria
    $params['contact_id'] = $triggerData->getContactId();
    return $params;
  }

  /**
   * Process the action
   *
   * @param CRM_Civirules_TriggerData_TriggerData $triggerData
   * @access public
   */
  public function processAction(CRM_Civirules_TriggerData_TriggerData $triggerData) {
    $entity = $this->getApiEntity();
    $action = $this->getApiAction();

    $actionParams = $this->getActionParameters();
    $groupIds = array();
    if (!empty($actionParams['group_id'])) {
      $groupIds = array($actionParams['group_id']);
    } elseif (!empty($actionParams['group_ids']) && is_array($actionParams['group_ids'])) {
      $groupIds = $actionParams['group_ids'];
    }
    foreach($groupIds as $groupId) {
      $params = array();
      $params['group_id'] = $groupId;

      //alter parameters by subclass
      $params = $this->alterApiParameters($params, $triggerData);

      //execute the action
      $this->executeApiAction($entity, $action, $params);
    }
  }

  /**
   * Returns a redirect url to extra data input from the user after adding a action
   *
   * Return false if you do not need extra data input
   *
   * @param int $ruleActionId
   * @return bool|string
   * @access public
   */
  public function getExtraDataInputUrl($ruleActionId) {
    return CRM_Utils_System::url('civicrm/civirule/form/action/groupcontact', 'rule_action_id='.$ruleActionId);
  }

  /**
   * Returns a user friendly text explaining the condition params
   * e.g. 'Older than 65'
   *
   * @return string
   * @access public
   */
  public function userFriendlyConditionParams() {
    $params = $this->getActionParameters();
    if (!empty($params['group_id'])) {
      $group = civicrm_api3('Group', 'getvalue', array('return' => 'title', 'id' => $params['group_id']));
      return $this->getActionLabel($group);
    } elseif (!empty($params['group_ids']) && is_array($params['group_ids'])) {
      $groups = '';
      foreach($params['group_ids'] as $group_id) {
        $group = civicrm_api3('Group', 'getvalue', array('return' => 'title', 'id' => $group_id));
        if (strlen($groups)) {
          $groups .= ', ';
        }
        $groups .= $group;
      }
      return $this->getActionLabel($groups);
    }
    return '';
  }

  /**
   * Method to set the api entity
   *
   * @return string
   * @access protected
   */
  protected function getApiEntity() {
    return 'GroupContact';
  }

  protected function getActionLabel($group) {
    switch ($this->getApiAction()) {
      case 'create':
        return ts('Add contact to group(s): %1', array(
          1 => $group
        ));
        break;
      case 'delete':
        return ts('Remove contact from group(s): %1', array(
          1 => $group
        ));
        break;
    }
    return '';
  }

}