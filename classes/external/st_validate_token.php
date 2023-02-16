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
 * Provides local_signuptoken\external\st_validate_token trait.
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
use enrol_token_plugin;

trait st_validate_token {

    public static function st_validate_token_parameters() {
		return new external_function_parameters(
			[
				"token" => new external_value(PARAM_TEXT, "The token value to validate"),
            ]
        );
    }

    public static function st_validate_token_returns() {
        return new external_single_structure(
            [
                'validated' => new external_value(PARAM_BOOL, 'Whether the key is validated or not.'),
                'warnings' => new external_warnings(),
            ]
        );
	}
    
	public static function st_validate_token($tokenValue) {

        $message = enrol_token_plugin::getTokenValidationErrors($tokenValue);

        if (empty($message)) {
            return [
                'validated' => true,
                'warnings' => null            
            ];
        } else {
            return [
                'validated' => false,
                'warnings' => new external_warnings($tokenValue, 'Token Value', $message)
            ];
        }
    }
}