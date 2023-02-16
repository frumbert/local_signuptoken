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
 * Saves and handle all Moodle settings related functionalities.
 *
 * @package     local_signuptoken
 * @copyright   2023 tim.stclair@gmail.com https://www.frumbert.org/
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . "/externallib.php");

class st_settings_handler {

    /**
     * Create external service with the provided name and the user id
     * @param  string $name   Name.
     * @param  int $userid User id.
     * @return array
     */
    public function st_create_external_service($name, $userid) {
        global $DB, $CFG;

        // Response initializations.
        $response               = array();
        $response['status']     = 1;
        $response['msg']        = '';
        $response['token']      = 0;
        $response['site_url']   = $CFG->wwwroot;
        $response['service_id'] = 0;

        // deja vu?
        if (isset($CFG->signuptoken_last_created_token)) {
            $response['service_id'] = $CFG->signuptoken_serviceid;
            $response['token'] = $CFG->signuptoken_last_created_token;
            return $response;
        }

        // Service creation default data.
        $service                       = array();
        $service['name']               = $name;
        $service['enabled']            = 1;
        $service['requiredcapability'] = null;
        $service['restrictedusers']    = 1;
        $service['component']          = null;
        $service['timecreated']        = time();
        $service['timemodified']       = null;

        $service['shortname']          = $this->st_generate_service_shortname();

        // User id validation.
        if (empty($userid)) {
            $response['status'] = 0;
            $response['msg']    = get_string('empty_userid_err', 'local_signuptoken');
            return $response;
        }

        // Creates unique shortname.
        if (empty($service['shortname'])) {
            $response['status'] = 0;
            $response['msg']    = get_string('create_service_shortname_err', 'local_signuptoken');
            return $response;
        }

        // Checks if the name is avaialble.
        if (!$this->st_check_if_service_name_available($name)) {
            $response['status'] = 0;
            $response['msg']    = get_string('create_service_name_err', 'local_signuptoken');
            return $response;
        }

        $service['downloadfiles'] = 0;
        $service['uploadfiles']   = 0;

        $serviceid = $DB->insert_record('external_services', $service);

        if ($serviceid) {
            // Add auth user.
            $this->st_add_auth_user($serviceid, $userid);
            // Adding functions in web service.
            $this->st_add_default_web_service_functions($serviceid);

            // Creating token iwith service id.
            $token = $this->st_create_token($serviceid, $userid);
            $response['service_id'] = $serviceid;
            $response['token'] = $token;
        } else {
            $response['status'] = 0;
            $response['msg']    = get_string('create_service_creation_err', 'local_signuptoken');
            return $response;
        }

        return $response;
    }

    /**
     * auto generates service shortname.
     * @return string new shortname.
     */
    public function st_generate_service_shortname() {
        global $DB;
        $shortname = 'signuptoken';
        $numtries  = 0;
        do {
            $numtries++;
            $newshortname = $shortname . $numtries;
            if ($numtries > 100) {
                return 0;
                break;
            }
        } while ($DB->record_exists('external_services', array('shortname' => $newshortname)));

        return $newshortname;
    }

    /**
     * checked if the provided service name is already regisered.
     * @param  string $servicename Service name.
     * @return boolean
     */
    public function st_check_if_service_name_available($servicename) {
        global $DB;
        if ($DB->record_exists('external_services', array('name' => $servicename))) {
            return 0;
        }
        return 1;
    }

    /**
     * Adds authorized user for the external service.
     * @param  int $serviceid Sevice Id.
     * @param  int $userid User id.
     */
    public function st_add_auth_user($serviceid, $userid) {
        global $DB;
        $dbarr = array();
        $dbarr['externalserviceid'] = $serviceid;
        $dbarr['userid'] = $userid;
        $dbarr['iprestriction'] = null;
        $dbarr['validuntil'] = null;
        $dbarr['timecreated'] = time();
        $DB->insert_record('external_services_users', $dbarr);
    }

    /**
     * This function adds default web services which registered with local_signuptoken only
     * @param  int $serviceid
     */
    public function st_add_default_web_service_functions($serviceid) {
        global $DB;
        $functions = array(
            array('externalserviceid' => $serviceid, 'functionname' => 'core_course_get_courses'),
            array('externalserviceid' => $serviceid, 'functionname' => 'core_course_get_categories'),
            array('externalserviceid' => $serviceid, 'functionname' => 'enrol_manual_enrol_users'),
            array('externalserviceid' => $serviceid, 'functionname' => 'core_enrol_get_users_courses'),

            array('externalserviceid' => $serviceid, 'functionname' => 'st_create_tokens'),
            array('externalserviceid' => $serviceid, 'functionname' => 'st_gen_accesstoken'),
            array('externalserviceid' => $serviceid, 'functionname' => 'st_validate_token'),
            array('externalserviceid' => $serviceid, 'functionname' => 'st_apply_token'),
        );

        foreach ($functions as $function) {
            if ($DB->record_exists('external_functions', array('name' => $function['functionname']))) {
                $DB->insert_record('external_services_functions', $function);
            }
        }
    }


    /**
     * This links the existing web service i.e it adds all the missing functions top the web-service
     * This does not add ayuth user.
     * @param  int $serviceid Service Id.
     * @param  int $token Token.
     * @return boolean returns success message.
     */
    public function st_link_exitsing_service($serviceid, $token) {
        $this->st_add_default_web_service_functions($serviceid);
        set_config('signuptoken_serviceid', $serviceid);
        return 1;
    }

    /**
     * This function creates the token by calling Moodles inbuilt function
     * @param  int $serviceid service id.
     * @param  int $userid    user id.
     * @return string Token
     */
    public function st_create_token($serviceid, $userid) {
        $tokentype   = EXTERNAL_TOKEN_PERMANENT; // Check this add for testing purpose.
        $contextorid = 1;

        // Default function of Moodle to create the token.
        $token = external_generate_token($tokentype, $serviceid, $userid, $contextorid);
        set_config("signuptoken_last_created_token", $token);
        set_config('signuptoken_serviceid', $serviceid);
        return $token;
    }
}
