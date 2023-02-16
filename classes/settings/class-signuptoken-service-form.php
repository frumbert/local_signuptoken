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
/**
 * Settings mod form
 *
 * @package     local_signuptoken
 * @copyright   2023 tim.stclair@gmail.com https://www.frumbert.org/
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once("$CFG->libdir/formslib.php");

/**
 * Used to create web service.
 */
class signuptoken_service_form extends moodleform {

    /**
     * Defining web services form.
     */
    public function definition() {
        global $CFG;

        $mform            = $this->_form;
        $existingservices = st_get_existing_services();
        $authusers        = st_get_administrators();
        $token            = isset($CFG->signuptoken_last_created_token) ? $CFG->signuptoken_last_created_token : ' - ';
        $service          = isset($CFG->signuptoken_serviceid) ? $CFG->signuptoken_serviceid : '';
        $tokenfield       = '';

        // 1st Field Service list
        $select = $mform->addElement(
            'select',
            'st_sevice_list',
            get_string('existing_serice_lbl', 'local_signuptoken'),
            $existingservices
        );
        $mform->addHelpButton('st_sevice_list', 'st_mform_service_desc', 'local_signuptoken');
        $select->setMultiple(false);

        // 2nd Field Service input name
        $mform->addElement(
            'text',
            'st_service_inp',
            get_string('new_service_inp_lbl', 'local_signuptoken'),
            array('class' => 'st_service_field')
        );
        $mform->setType('st_service_inp', PARAM_TEXT);

        // 3rd field Users List.
        $select = $mform->addElement(
            'select',
            'st_auth_users_list',
            get_string('new_serivce_user_lbl', 'local_signuptoken'),
            $authusers,
            array('class' => '')
        );
        $select->setMultiple(false);

        // If service is empty then show just the blank text with dash.
        $tokenfield = $token;

        if (!empty($service)) {
            // If the token available then show the token.
            $tokenfield = st_create_token_field($service, $token);
        }

        // 5th field Token
        $mform->addElement(
            'static',
            'st_mform_token_wrap',
            get_string('token', 'local_signuptoken'),
            '<b id="id_st_token_wrap">' . $tokenfield . '</b>'
        );

        $mform->addHelpButton('st_mform_token_wrap', 'st_mform_token_desc', 'local_signuptoken');
        $mform->addElement(
            'static',
            'st_mform_common_error',
            '',
            '<div id="st_common_err"></div><div id="st_common_success"></div>'
        );
        $mform->addElement('button', 'st_mform_create_service', get_string("link", 'local_signuptoken'));

        if (!class_exists('webservice')) {
            require_once($CFG->dirroot . "/webservice/lib.php");
        }

        // Set default values.
        if (!empty($service)) {
            $mform->setDefault("st_sevice_list", $service);
        }
    }
}
