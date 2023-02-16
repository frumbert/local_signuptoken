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
 * Provides local_signuptoken\external\st_create_accesstoken trait.
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
use moodle_exception;
use context_system;

trait st_create_accesstoken {

    public static function st_create_accesstoken_parameters() {
		return new external_function_parameters(
			array(
				"id" => new external_value(PARAM_INT, "The moodle user id"),
                "serviceshortname" => new external_value(PARAM_TEXT, "The service shortname"),
            )
        );
    }

    public static function st_create_accesstoken_returns() {
		return new external_single_structure(
			[
				'token' => new external_value(PARAM_TEXT, "webservice token")
            ]
		);
	}

	public static function st_create_accesstoken($userid, $serviceshortname) {
        global $DB;

        $user = $DB->get_record('user', ['id' => $userid], 'id', MUST_EXIST);

        $context = context_system::instance();
        $service = $DB->get_record('external_services', array('shortname' => $serviceshortname, 'enabled' => 1));

        if (empty($service)) {
            throw new moodle_exception('servicenotavailable', 'webservice');
        }

        $token = external_generate_token(EXTERNAL_TOKEN_PERMANENT, $service, $user->id, $context);
        return $token;

    }
}
