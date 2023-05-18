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
 * Provides local_signuptoken\external\st_apply_token trait.
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
use external_warnings;

trait st_apply_token {

    public static function st_apply_token_parameters() {
        return new external_function_parameters(
            array(
                "userid" => new external_value(PARAM_INT, "The user to apply the token to"),
                "token" => new external_value(PARAM_TEXT, "The token value to apply"),
            )
        );
    }

    public static function st_apply_token_returns() {
        return new external_single_structure(
            [
                'applied' => new external_value(PARAM_BOOL, 'Whether the token was applied or not.'),
                'warnings' => new external_value(PARAM_TEXT, 'Validator error message .'), // new external_warnings(),
            ]
        );
    }
    
    public static function st_apply_token($userid, $tokenValue) {
    global $DB, $CFG;
        $user = $DB->get_record('user', ['id'=>$userid]);
        require_once($CFG->dirroot.'/enrol/token/lib.php');
        $etp = new \enrol_token_plugin();
        if ($etp->perform_trusted_enrolment($tokenValue, $user)) {
            return [
                'applied' => true,
                'warnings' => ''
            ];
        } else {
            return [
                'applied' => false,
                'warnings' => get_string('apply_token_error', 'local_signuptoken') // new external_warnings('Token Value', $tokenValue)
            ];
        }
    }

}