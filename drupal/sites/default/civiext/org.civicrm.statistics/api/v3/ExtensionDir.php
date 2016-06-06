<?php

function civicrm_api3_extension_dir_get($params)
{
  // Query the Drupal database
  $result = db_query(
    "SELECT node.nid, node.title, node.created,
            field_data_field_extension_fq_name.field_extension_fq_name_value AS fq_name,
            field_data_field_git_url.field_git_url_value AS git_url
       FROM {node} node
            LEFT JOIN {field_data_field_extension_fq_name} field_data_field_extension_fq_name
              ON node.nid = field_data_field_extension_fq_name.entity_id
                 AND (field_data_field_extension_fq_name.entity_type = 'node' AND field_data_field_extension_fq_name.deleted = '0')
            LEFT JOIN {field_data_field_git_url} field_data_field_git_url
              ON node.nid = field_data_field_git_url.entity_id
                 AND (field_data_field_git_url.entity_type = 'node' AND field_data_field_git_url.deleted = '0')
      WHERE node.status = '1' AND node.type = 'extension'"
    )->fetchAll(PDO::FETCH_ASSOC);
  // Return result
  return civicrm_api3_create_success($result);
}