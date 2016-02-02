<?php

/**
 * Util functions for custom fields.
 */
class CRM_Civirules_Utils_CustomField {

    /**
     * Returns which custom field html_type is a multiselect
     *
     * @return array
     */
    public static function getMultiselectTypes() {
        return array('CheckBox', 'Multi-Select', 'AdvMulti-Select');
    }

    /**
     * Returns whether a custom field is multi select field.
     * @param $customfield_id
     * @return bool
     * @throws CiviCRM_API3_Exception
     */
    public static function isCustomFieldMultiselect($customfield_id) {
        $multi_select_types = self::getMultiselectTypes();
        $custom_field = civicrm_api3('CustomField', 'getsingle', array('id' => $customfield_id));
        if (in_array($custom_field['html_type'], $multi_select_types)) {
            return true;
        }
        return false;
    }

}