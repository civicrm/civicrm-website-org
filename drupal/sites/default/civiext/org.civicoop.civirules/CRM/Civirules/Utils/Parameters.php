<?php

class CRM_Civirules_Utils_Parameters {

  /**
   * Converts a multiline string to an array
   *
   * E.g the input is:
   * group_id=12
   * contact_id=1
   *
   * Output:
   * array(
   *  'group_id' => 12,
   *  'contact_id' => 1,
   * );
   *
   * @param $parameters
   * @return array
   */
  public static function convertFromMultiline($parameters) {
    $converted_parameters = array();

    $parameters_per_line = preg_split('/\r\n|\r|\n/', $parameters);
    foreach($parameters_per_line as $line) {
      $parameter = preg_split('/=/', $line);
      if (isset($parameter[0]) && isset($parameter[1])) {
        $field = trim($parameter[0]);
        $value = trim($parameter[1]);
        $converted_parameters[html_entity_decode($field)] = html_entity_decode($value);
      }
    }

    return $converted_parameters;
  }

  /**
   * Converts an array to a multiline string
   *
   * E.g
   * the input:
   * array(
   *  'group_id' => 12,
   *  'contact_id' => 1,
   * );
   *
   * the output is:
   * group_id=12
   * contact_id=1
   *
   * @param array $parameters
   * @return string
   */
  public static function convertToMultiline($parameters) {
    $converted_parameters = '';
    foreach($parameters as $key => $value) {
      if (strlen($converted_parameters)) {
        $converted_parameters .= "\r\n";
      } else {
        $converted_parameters .= "";
      }
      $converted_parameters .= htmlentities($key).'='.htmlentities($value);
    }
    return $converted_parameters;
  }

}