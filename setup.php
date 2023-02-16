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

require('../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/local/signuptoken/classes/settings/class-signuptoken-service-form.php');

global $CFG, $COURSE, $PAGE;

$PAGE->requires->jquery();

admin_externalpage_setup('signuptoken_setup');

$stringmanager = get_string_manager();
$strings = $stringmanager->load_component_strings('local_signuptoken', 'en');
$PAGE->requires->strings_for_js(array_keys($strings), 'local_signuptoken');

require_login();
$context = context_system::instance();
$baseurl = $CFG->wwwroot . '/local/signuptoken/setup.php';

$PAGE->set_pagelayout('admin');
$PAGE->set_context($context);
$PAGE->set_url('/local/signuptoken/setup.php');

$PAGE->set_title(get_string('st-setting-page-title', 'local_signuptoken'));
$PAGE->requires->js_call_amd("local_signuptoken/setup", "init");

$form = new signuptoken_service_form();

if ($form->get_data()) {

}

echo $OUTPUT->header();
echo $OUTPUT->container_start();

$form->display();

echo $OUTPUT->container_end();
echo $OUTPUT->footer();
