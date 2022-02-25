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
    // so there isn't much we can do here
}