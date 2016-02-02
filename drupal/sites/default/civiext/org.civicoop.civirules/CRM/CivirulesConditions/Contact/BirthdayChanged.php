<?php
/**
 * Class for CiviRules AgeComparison (extending generic ValueComparison)
 *
 * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
 * @license AGPL-3.0
 */

class CRM_CivirulesConditions_Contact_BirthdayChanged extends CRM_CivirulesConditions_Generic_FieldChanged {

  /**
   * Returns name of entity
   *
   * @return string
   * @access protected
   */
  protected function getEntity() {
    return 'contact';
  }

  /**
   * Returns name of the field
   *
   * @return string
   * @access protected
   */
  protected function getField() {
    return 'birth_date';
  }

  /**
   * This method could be overridden in subclasses to
   * transform field data to a certain type
   *
   * E.g. a date field could be transformed to a DataTime object so that
   * the comparison is easier
   *
   * @param $fieldData
   * @return mixed
   * @access protected
   */
  protected function transformFieldData($fieldData) {
    return new DateTime($fieldData);
  }

}