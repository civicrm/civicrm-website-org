<?php

interface CRM_Civirules_TriggerData_Interface_OriginalData {

  /**
   * Returns an array with the original data
   *
   * @return array
   */
  public function getOriginalData();

  /**
   * Returns name of original entity
   *
   * @return string
   */
  public function getOriginalEntity();

}