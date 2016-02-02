<?php

return array (
  0 =>
    array (
      'name' => 'Cron:CiviRuleAction.Process',
      'entity' => 'Job',
      'params' =>
        array (
          'version' => 3,
          'name' => 'Process delayed civirule actions',
          'description' => '',
          'run_frequency' => 'Always',
          'api_entity' => 'CiviRuleAction',
          'api_action' => 'Process',
          'parameters' => '',
          'is_active' => '1',
        ),
    ),
);