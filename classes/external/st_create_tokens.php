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
 * Provides local_signuptoken\external\st_create_tokens trait.
 *
 * @package     local_signuptoken
 * @category    external
 * @copyright   2023 tim.stclair@gmail.com https://www.frumbert.org/
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_signuptoken\external;

defined('MOODLE_INTERNAL') || die();

use external_function_parameters;
use external_multiple_structure;
use external_single_structure;
use external_value;
use moodle_exception;
use context_system;
use context_user;
use Exception;
use stdClass;

trait st_create_tokens {

	public static function st_create_tokens_parameters() {
		return new external_function_parameters(
			array(
				"course" => new external_value(PARAM_TEXT, "The course idnumber (alphanumeric - not the row id) you want to generate tokens for"),
				"seats" => new external_value(PARAM_INT, "The amount of token you want to generate (1-500)", VALUE_DEFAULT, 1),
				"places" => new external_value(PARAM_INT, "The amount of times a token can be re-used (1-500)", VALUE_DEFAULT, 1),
				"expiry" => new external_value(PARAM_INT, "The expiry date (as unix timestamp)", VALUE_DEFAULT, 0),
				"prefix" => new external_value(PARAM_TEXT, "Prefix tokens by this (0-4 character) string", VALUE_DEFAULT, ""),
				"cohort" => new external_value(PARAM_TEXT, "The cohort idnumber (alphanumeric - not the row id) you want to add the tokens for (created if missing)", VALUE_DEFAULT, "")
			  )
		);
	}

	public static function st_create_tokens_returns() {
    	// return {"token" : [value1,value2,value3]}
		return new external_single_structure(
			array(
				'token' => new external_multiple_structure(
					new external_value(PARAM_TEXT, 'token code')
				)
			)
		);
	}
	
	public static function st_create_tokens($course_idnumber, $num_seats = 1, $places_per_seat = 1, $expiry = 0, $prefix = "", $cohort_idnumber = "local_signuptoken_webservice") {
		global $USER, $DB;

        $context = context_user::instance_by_id($USER->id);
		try {
			self::validate_context($context);
		} catch (Exception $e) {
        	$exceptionparam = new stdClass();
			$exceptionparam->message = $e->getMessage();
			throw new moodle_exception('errorcatcontextnotvalid', 'webservice', '', $exceptionparam);
		}
		$syscontext = context_system::instance();
		require_capability('block/enrol_token_manager:createtokens', $syscontext);

		$params = self::validate_parameters(self::st_create_tokens_parameters(), array(
			"course" => $course_idnumber,
			"seats" => $num_seats,
			"places" => $places_per_seat,
			"expiry" => $expiry,
			"prefix" => $prefix,
			"cohort" => $cohort_idnumber
		));

		$tokens = enrol_token_manager_create_tokens_external($course_idnumber, $num_seats, $places_per_seat, $expiry, $prefix, $cohort_idnumber);

		return array("token" => $tokens);

	}

}