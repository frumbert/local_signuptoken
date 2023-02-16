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
 * Provides local_signuptoken\external\api class.
 *
 * @package     local_signuptoken
 * @copyright   2023 tim.stclair@gmail.com https://www.frumbert.org/
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$functions = array(
    'st_create_service' => array(
        'classname'     => 'local_signuptoken\external\api',
        'methodname'    => 'st_create_service',
        'description'   => 'Create web service',
        'type'          => 'read',
        'ajax'          => true,
    ),
    'st_link_service' => array(
        'classname'     => 'local_signuptoken\external\api',
        'methodname'    => 'st_link_service',
        'description'   => 'Update web service',
        'type'          => 'read',
        'ajax'          => true,
    ),
    'st_create_tokens' => array(
        'classname'     => 'local_signuptoken\external\api',
        'methodname'    => 'st_create_tokens',
        'description'   => 'Generate tokens',
        'type'          => 'write',
        'ajax'          => true,
    ),
    'st_gen_accesstoken' => array(
        'classname'     => 'local_signuptoken\external\api',
        'methodname'    => 'st_create_accesstoken',
        'description'   => 'Generate a user access token',
        'type'          => 'write',
        'ajax'          => true,
    ),
    'st_validate_token' => array(
        'classname'     => 'local_signuptoken\external\api',
        'methodname'    => 'st_validate_token',
        'description'   => 'Validate a token',
        'type'          => 'read',
        'ajax'          => true,
    ),
    'st_apply_token' => array(
        'classname'     => 'local_signuptoken\external\api',
        'methodname'    => 'st_apply_token',
        'description'   => 'Apply a token to a user',
        'type'          => 'write',
        'ajax'          => true,
    ),
);
