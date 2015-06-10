<?php

/**
 * Involvement.Update API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRM/API+Architecture+Standards
 */
function _civicrm_api3_involvement_Update_spec(&$spec) {
    $spec['contact_id']['api.required'] = 1;
}

/**
 * Involvement.Update API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_involvement_Update($params) {

    $score = 0;

    //is the contact a member? - what type? Give a score based on the amount of $ that their membership cost / 10
    $membershipScores = array(
        '1' => '2',
        '2' => '4',
        '3' => '6',
        '12' => '6',
        '13' => '4',
        '14' => '2'
    );


    $mp = array(
        'contact_id' => $params['contact_id'],
        'status_id' => array('IN' => array(1, 2, 3))
    );

    $mr = civicrm_api3('Membership', 'get', $mp);

    foreach($mr['values'] as $membership) {
        $score =+ $membershipScores[$membership['membership_type_id']];
    }

    $gr = civicrm_api3('GroupContact', 'get', array(
        'contact_id' => $params['contact_id'],
        'status' => "Added"
    ));

    //are they an active contributor? add 1 to their score
    foreach($gr['values'] as $group) {
        if($group['group_id']==131){
            $score ++;
        }
    }
    //
    civicrm_api3('CustomValue', 'create', array(
    'sequential' => 1,
    'entity_id' => $params['contact_id'],
    'custom_183' => $score
    ));
    return civicrm_api3_create_success();
}

