<?php

return array (
  0 =>
    array (
      'name' => 'Civirules:Action.TagAdd',
      'entity' => 'CiviRuleAction',
      'params' =>
        array (
          'version' => 3,
          'name' => 'TagAdd',
          'label' => 'Add tag to contact',
          'class_name' => 'CRM_CivirulesActions_Tag_Add',
          'is_active' => 1
        ),
    ),
  1 => array (
    'name' => 'Civirules:Action.TagRemove',
    'entity' => 'CiviRuleAction',
    'params' =>
      array (
        'version' => 3,
        'name' => 'TagRemove',
        'label' => 'Remove tag from contact',
        'class_name' => 'CRM_CivirulesActions_Tag_Remove',
        'is_active' => 1
      ),
  ),
);