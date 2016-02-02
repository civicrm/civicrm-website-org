<?php

return array (
  0 =>
    array (
      'name' => 'Civirules:Action.GroupContactAdd',
      'entity' => 'CiviRuleAction',
      'params' =>
        array (
          'version' => 3,
          'name' => 'GroupContactAdd',
          'label' => 'Add contact to group',
          'class_name' => 'CRM_CivirulesActions_GroupContact_Add',
          'is_active' => 1
        ),
    ),
  1 => array (
    'name' => 'Civirules:Action.GroupContactRemove',
    'entity' => 'CiviRuleAction',
    'params' =>
      array (
        'version' => 3,
        'name' => 'GroupContactRemove',
        'label' => 'Remove contact from group',
        'class_name' => 'CRM_CivirulesActions_GroupContact_Remove',
        'is_active' => 1
      ),
  ),
);