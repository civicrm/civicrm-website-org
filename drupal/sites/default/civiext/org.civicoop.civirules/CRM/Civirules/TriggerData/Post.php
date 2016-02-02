<?php

class CRM_Civirules_TriggerData_Post extends CRM_Civirules_TriggerData_TriggerData {

  protected $entity;

  public function __construct($entity, $objectId, $data) {
    parent::__construct();

    $this->entity = $entity;

    $this->setEntityData($entity, $data);
    if ($entity == 'contact') {
      $this->contact_id = $objectId;
    } elseif (isset($data['contact_id'])) {
      $this->contact_id = $data['contact_id'];
    }
  }

  public function getEntity() {
    return $this->entity;
  }

}