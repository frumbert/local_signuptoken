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
 * Provides local_signuptoken\external\st_create_service trait.
 *
 * @package     local_signuptoken
 * @category    external
 * @copyright   2023 tim.stclair@gmail.com https://www.frumbert.org/
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_signuptoken\external;

defined('MOODLE_INTERNAL') || die();

use external_function_parameters;
use external_single_structure;
use external_value;

require_once($CFG->dirroot . '/local/signuptoken/classes/class-settings-handler.php');

/**
 * Trait implementing the external function local_signuptoken_course_progress_data
 */
trait st_create_service {

    /**
     * functionality to create new external service
     * @param  string $webservicename
     * @param  int $userid
     * @return boolean
     */
    public static function st_create_service($webservicename, $userid) {
        $settingshandler = new \st_settings_handler();
        $response = $settingshandler->st_create_external_service($webservicename, $userid);
        return $response;
    }

    /**
     * Paramters defined for create service function.
     */
    public static function st_create_service_parameters() {
        return new external_function_parameters(
            array(
                'web_service_name' => new external_value(PARAM_TEXT, get_string('web_service_name', 'local_signuptoken')),
                'user_id'          => new external_value(PARAM_TEXT, get_string('web_service_auth_user', 'local_signuptoken'))
            )
        );
    }

    /**
     * paramters which will be returned from create service function.
     */
    public static function st_create_service_returns() {
        return new external_single_structure(
            array(
                'token'     => new external_value(PARAM_TEXT, get_string('web_service_token', 'local_signuptoken')),
                'site_url'  => new external_value(PARAM_TEXT, get_string('moodle_url', 'local_signuptoken')),
                'service_id'  => new external_value(PARAM_INT, get_string('web_service_id', 'local_signuptoken')),
                'status'  => new external_value(PARAM_INT, get_string('web_service_creation_status', 'local_signuptoken')),
                'msg'  => new external_value(PARAM_TEXT, get_string('web_service_creation_msg', 'local_signuptoken'))
            )
        );
    }
}
