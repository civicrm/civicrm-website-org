<?php
/**
 * Class for CiviRules CronTrigger Birthday
 *
 * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
 * @license AGPL-3.0
 */

class CRM_CivirulesCronTrigger_Birthday extends CRM_Civirules_Trigger_Cron {

  private $dao = false;

  /**
   * Returns an array of entities on which the t riggerreacts
   *
   * @return CRM_Civirules_TriggerData_EntityDefinition
   */
  protected function reactOnEntity() {
    return new CRM_Civirules_TriggerData_EntityDefinition(ts('Person'), 'contact', 'CRM_Contact_DAO_Contact', 'Contact');
  }

  /**
   * This method returns a CRM_Civirules_TriggerData_TriggerData this entity is used for triggering the rule
   *
   * Return false when no next entity is available
   *
   * @return object|bool CRM_Civirules_TriggerData_TriggerData|false
   * @access protected
   */
  protected function getNextEntityTriggerData() {
    if (!$this->dao) {
      $this->queryForTriggerEntities();
    }
    if ($this->dao->fetch()) {
      $data = array();
      CRM_Core_DAO::storeValues($this->dao, $data);
      $triggerData = new CRM_Civirules_TriggerData_Cron($this->dao->id, 'contact', $data);
      return $triggerData;
    }
    return false;
  }

  /**
   * Method to query trigger entities
   *
   * @access private
   */
  private function queryForTriggerEntities() {
    $sql = "SELECT c.*
            FROM `civicrm_contact` `c`
            WHERE `c`.`birth_date` IS NOT NULL
            AND DAY(`c`.`birth_date`) = DAY(NOW())
            AND MONTH(`c`.`birth_date`) = MONTH(NOW())
            AND c.is_deceased = 0 and c.is_deleted = 0
            AND `c`.`id` NOT IN (
              SELECT `rule_log`.`contact_id`
              FROM `civirule_rule_log` `rule_log`
              WHERE `rule_log`.`rule_id` = %1 AND DATE(`rule_log`.`log_date`) = DATE(NOW())
            )";
    $params[1] = array($this->ruleId, 'Integer');
    $this->dao = CRM_Core_DAO::executeQuery($sql, $params, true, 'CRM_Contact_BAO_Contact');
  }
}