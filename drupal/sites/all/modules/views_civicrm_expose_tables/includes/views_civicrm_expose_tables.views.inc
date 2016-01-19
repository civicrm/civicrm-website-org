<?php
/**
 * Implements hook_views_data().
 */

function views_civicrm_expose_tables_views_data() {
  //get table information
  $tables = array();
  $pre_tables = array();
  $cividb = variable_get('views_civicrm_expose_tables_civicrm_database', ''); 
  $pre_tables = variable_get('views_civicrm_expose_tables_prefix_tables');


  if($cividb != '') {
    foreach($pre_tables as $key => $table) {
      if($table){
        $tables[$key] = '`' . $cividb . '`.`' . $table . '`';
      }
    }
  } 
 
  //if tables is empty then return;
  if(count($tables)==0) return;

  $data = array();
  foreach($tables as $k => $table){

  if($table){
    trim($table);

    $fields = array();
	
	  $result = db_query("SHOW COLUMNS FROM ". $table );
    if (!$result) {
      echo 'Could not run query: ' . mysql_error();
      exit;
    }
    if ($result->rowCount()) {
      while ($col = $result->fetchObject()) {
		   $fields[] = $col;
      }//end while row
    } //end if row count

    // Define the base group of this table. Fields that don't
    // have a group defined will go into this field by default.
    $data[$k]['table']['group'] = $k;
 
    //Query for the primary key
    $key_result = db_query("SHOW KEYS FROM " . $table . " WHERE Key_name = 'PRIMARY'");
	  //if a primary key is found setup the table as a base table 
	  if($key_result->rowCount()){
	    $key = $key_result->fetchObject();
	
	    $data[$k]['table']['base'] = array(
        'field' => $key->Column_name,
        'title' => $k,
        'weight' => -10,
	    );
	  }

	  //run through fields and make default settings
	  foreach($fields as $field){
	 		if(stristr($field->Type, "int") 
			|| stristr($field->Type, "float") 
			|| stristr($field->Type, "double") 
			|| stristr($field->Type, "decimal") 
			|| stristr($field->Type, "numeric") ){
			
		     $filter_handler = 'views_handler_filter_numeric';
			   $sort_handler = 'views_handler_sort';
			   $field_handler = 'views_handler_field_numeric';
			   $argument_handler = 'views_handler_argument_numeric';
			}
  		elseif (stristr($field->Type, "char") || stristr($field->Type, "text")) {
	     $filter_handler = 'views_handler_filter_string';
			 $sort_handler = 'views_handler_sort';
			 $field_handler = 'views_handler_field';
			 $argument_handler = 'views_handler_argument_string';
			}
  		elseif (stristr($field->Type, "time") || stristr($field->Type, "date") || stristr($field->Type, "year")){
	      
        $filter_handler = 'civicrm_handler_filter_datetime';
        $sort_handler = 'civicrm_handler_sort_date';
				$field_handler = 'civicrm_handler_field_datetime';
      	$argument_handler = 'views_handler_argument_civicrm_fulldate';
			}
  		else{ 
        $filter_handler = 'views_handler_filter';
			  $sort_handler = 'views_handler_sort';
			  $field_handler = 'views_handler_field';
			  $argument_handler = 'views_handler_argument';
			}
		
		if($field->Field=='timestamp' && stristr($field->Type, "int") ) {
		  $filter_handler = 'views_handler_filter_date';
				$sort_handler = 'views_handler_sort_date';
				$field_handler = 'views_handler_field_date';
				$argument_handler = 'views_handler_argument_date';
		
		}
		
	  $data[$k][$field->Field] = array (
		  'title' => $field->Field,
      'help' => t($field->Field . ' data'), 
      'field' => array(
      'handler' => $field_handler,
      'click sortable' => TRUE,
    ),
      'argument' => array(
        'handler' => $argument_handler,
       ),
 	    'filter' => array(
        'handler' => $filter_handler,
      ),
	    'sort' => array(
        'handler' => $sort_handler,
      ),
		);
	 }
	}
}	
return $data;
}



