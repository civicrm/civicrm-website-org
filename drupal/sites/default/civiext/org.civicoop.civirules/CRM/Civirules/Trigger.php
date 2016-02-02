<?php

abstract class CRM_Civirules_Trigger {

  protected $ruleId;

  protected $triggerId;

  protected $triggerParams;

  /**
   * @var string
   */
  protected $ruleTitle;

  public function setRuleId($ruleId) {
    $this->ruleId = $ruleId;
  }

  public function setTriggerParams($triggerParams) {
    $this->triggerParams = $triggerParams;
  }

  public function getRuleId() {
    return $this->ruleId;
  }

  public function setTriggerId($triggerId) {
    $this->triggerId = $triggerId;
  }

  public function getTriggerId() {
    return $this->triggerId;
  }

  public function getRuleTitle() {
    if (empty($this->ruleTitle) && !empty($this->ruleId)) {
      $rule = new CRM_Civirules_BAO_Rule();
      $rule->id = $this->ruleId;
      if ($rule->find(true)) {
        $this->ruleTitle = $rule->label;
      }
    }
    return $this->ruleTitle;
  }

  /**
   * Returns an array of entities on which the trigger reacts
   *
   * @return CRM_Civirules_TriggerData_EntityDefinition
   */
  abstract protected function reactOnEntity();


  public function getProvidedEntities() {
    $additionalEntities = $this->getAdditionalEntities();
    foreach($additionalEntities as $entity) {
      $entities[$entity->key] = $entity;
    }

    $entity = $this->reactOnEntity();
    $entities[$entity->key] = $entity;

    return $entities;
  }

  /**
   * Returns an array of additional entities provided in this trigger
   *
   * @return array of CRM_Civirules_TriggerData_EntityDefinition
   */
  protected function getAdditionalEntities() {
    $reactOnEntity = $this->reactOnEntity();
    $entities = array();
    if (strtolower($reactOnEntity->key) != strtolower('Contact')) {
      $entities[] = new CRM_Civirules_TriggerData_EntityDefinition('Contact', 'Contact', 'CRM_Contact_DAO_Contact', 'Contact');
    }
    return $entities;
  }

  /**
   * Returns a redirect url to extra data input from the user after adding a trigger
   *
   * Return false if you do not need extra data input
   *
   * @param int $ruleId
   * @return bool|string
   * @access public
   * @abstract
   */
  public function getExtraDataInputUrl($ruleId) {
    return false;
  }

  /**
   * Returns a description of this trigger
   *
   * @return string
   * @access public
   * @abstract
   */
  public function getTriggerDescription() {
    return '';
  }

  /**
   * Alter the trigger data with extra data
   *
   * @param \CRM_Civirules_TriggerData_TriggerData $triggerData
   */
  public function alterTriggerData(CRM_Civirules_TriggerData_TriggerData &$triggerData) {
    $hook_invoker = CRM_Civirules_Utils_HookInvoker::singleton();
    $hook_invoker->hook_civirules_alterTriggerData($triggerData);
  }

}