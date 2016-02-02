<?php

class CRM_Civirules_TriggerData_EntityDefinition {

  /**
   * Label of the entity might be shown to the user
   *
   * @var string
   */
  public $label;

  /**
   * Entity type e.g. contact, contribution, event, participant etc...
   *
   * @var string
   */
  public $entity;

  /**
   * DAO class name e.g. CRM_Contact_DAO_Contact or CRM_Contribution_DAO_Contribution
   *
   * @var string
   */
  public $daoClass;

  /**
   * Key of this entity in the trigger e.g. contact, individual, first_contribution etc...,
   *
   * @var string
   */
  public $key;

  public function __construct($label, $entity, $daoClass='', $key='') {
    $this->label = $label;
    $this->entity = $entity;
    $this->daoClass = $daoClass;
    $this->key = $entity;
    if (!empty($key)) {
      $this->key = $key;
    }
  }

}