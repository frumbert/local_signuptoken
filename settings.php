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
 * Plugin administration pages are defined here.
 *
 * @package     local_signuptoken
 * @copyright   2023 tim.stclair@gmail.com https://www.frumbert.org/
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once(dirname(__FILE__) . '/lib.php');

$ADMIN->add(
    'modules',
    new admin_category(
        'signuptokengroup',
        new lang_string(
            'pluginname',
            'local_signuptoken'
        )
    )
);

$ADMIN->add(
    'signuptokengroup', // 'localplugins',
    new admin_externalpage(
        'signuptoken_setup',
        new lang_string(
            'nav_name',
            'local_signuptoken'
        ),
        "$CFG->wwwroot/local/signuptoken/setup.php",
        array(
            'moodle/user:update',
            'moodle/user:delete'
        )
    )
);
$settings = new admin_settingpage('signuptoken_settings', new lang_string('pluginname', 'local_signuptoken'));
$ADMIN->add('localplugins', $settings);

$settings->add(
    new admin_setting_heading(
        'local_signuptoken/st_settings_msg',
        '',
        '<div class="st-settings-launch-button" style="padding-left:1rem;">' . get_string('st_settings_msg', 'local_signuptoken')
            . '<a target="_blank" class="btn btn-info ml-3"'
            . ' href="' . $CFG->wwwroot . '/local/signuptoken/setup.php'
            . '" >' . get_string('click_here', 'local_signuptoken') . '</a></div>'
    )
);
