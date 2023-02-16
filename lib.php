<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

// inject the 'token' field after password field (before 'more details' header, which is called 'supplyinfo')
function local_signuptoken_extend_signup_form($mform) {
    $el = $mform->createElement('text', 'token', get_string('token', 'local_signuptoken'));
    $mform->insertElementBefore($el, 'supplyinfo');
    $mform->setType('token', PARAM_TEXT);
    $mform->addHelpButton('token', 'token', 'local_signuptoken');
}

// validate the signup request, return an array of errors or null
function local_signuptoken_validate_extend_signup_form($data) {
    global $DB, $CFG;
    $errors = array();
    $token = trim($data['token'] ?? '');

    // validate the token code, if set
    if (!empty($token)) {
        require_once($CFG->dirroot . '/enrol/token/lib.php');
        $tokenErrors = enrol_token_plugin::getTokenValidationErrors($token);
        if (!empty($tokenErrors)) $errors['token'] = $tokenErrors;
    }

    return $errors;
}

// STUB: for potential future use
function local_signuptoken_post_signup_requests($user) {
    // this is executed AFTER the form validates, but before $user is created
    // we could raise an event here to log that the valid user is ready for creation ... 
}

/**
 * returns the list of courses in which user is enrolled
 *
 * @param int $userid user id.
 * @return array array of courses.
 */
function local_signuptoken_get_array_of_enrolled_courses($userid) {
    $enrolledcourses = enrol_get_users_courses($userid);
    $courses         = array();

    foreach ($enrolledcourses as $value) {
        array_push($courses, $value->id);
    }
    return $courses;
}

/**
 * Functionality to get all available Moodle sites services.
 */
function st_get_existing_services() {
    global $DB;
    $settingsarr           = array();
    $result                = $DB->get_records("external_services", null, '', 'id, name');
    $settingsarr['']       = get_string('existing_serice_lbl', 'local_signuptoken');
    $settingsarr['create'] = ' - ' . get_string('new_web_new_service', 'local_signuptoken') . ' - ';

    foreach ($result as $value) {
        $settingsarr[$value->id] = $value->name;
    }

    return $settingsarr;
}

/**
 * Functionality to get all available Moodle sites tokens.
 *
 * @param int $serviceid service id.
 * @return array settings array.
 */
function st_get_service_tokens($serviceid) {
    global $DB;

    $settingsarr = array();
    $result      = $DB->get_records("external_tokens", null, '', 'token, externalserviceid');

    foreach ($result as $value) {
        $settingsarr[] = array('token' => $value->token, 'id' => $value->externalserviceid);
    }

    return $settingsarr;
}

/**
 * Functionality to get all available Moodle sites administrator.
 */
function st_get_administrators() {
    $admins          = get_admins();
    $settingsarr      = array();
    $settingsarr[''] = get_string('new_serivce_user_lbl', 'local_signuptoken');

    foreach ($admins as $value) {
        $settingsarr[$value->id] = $value->email;
    }
    return $settingsarr;
}

/**
 * Functionality to create token.
 *
 * @param int $serviceid service id.
 * @param int $existingtoken existing token.
 * @return string html content.
 */
function st_create_token_field($serviceid, $existingtoken = '') {

    $tokenslist = st_get_service_tokens($serviceid);

    $html = '<div class="st_copy_txt_wrap">
                <div style="width:60%;">
                    <select class="st_copy" class="custom-select" name="st_token" id="id_st_token">
                    <option value="">' . get_string('token_dropdown_lbl', 'local_signuptoken') . '</option>';

    foreach ($tokenslist as $token) {
        $selected = '';
        $display = '';

        if (isset($token['token']) && $token['token'] == $existingtoken) {
            $selected = " selected";
        }

        if (isset($token['id']) && $token['id'] != $serviceid) {
            $display = 'style="display:none"';
        }

        $html .= '<option data-id="' . $token['id'] . '" value="' . $token['token'] . '" '
            . $display . " " . $selected . '>' . $token['token'] . '</option>';
    }

    $html .= '      </select>
                </div>
                <div> <button class="btn btn-primary st_primary_copy_btn">' . get_string('copy', 'local_signuptoken')
        . '</button> </div>
            </div>';

    return $html;
}