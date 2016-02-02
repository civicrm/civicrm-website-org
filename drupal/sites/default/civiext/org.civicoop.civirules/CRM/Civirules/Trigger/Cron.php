<?php

abstract class CRM_Civirules_Trigger_Cron extends CRM_Civirules_Trigger {

  /**
   * This function returns a CRM_Civirules_TriggerData_TriggerData this entity is used for triggering the rule
   *
   * Return false when no next entity is available
   *
   * @return CRM_Civirules_TriggerData_TriggerData|false
   */
  abstract protected function getNextEntityTriggerData();

  /**
   * @return int
   */
  public function process() {
    $count = 0;
    $isValidCount = 0;
    while($triggerData = $this->getNextEntityTriggerData()) {
      $this->alterTriggerData($triggerData);
      $isValid = CRM_Civirules_Engine::triggerRule($this, $triggerData);
      if ($isValid) {
        $isValidCount++;
      }
      $count ++;
    }
    return array(
      'count' => $count,
      'is_valid_count' => $isValidCount,
    );
  }


}