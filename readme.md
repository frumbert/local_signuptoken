# Signup Token plugin

## Info

This plugin adds a token field to the Email-Based self-registration page.

It also adds a set of web services for creating, validating and enrolling a user with a token.

## Requirements

 * You need to be using auth/email (Email-Based self-registration)
 * You need https://github.com/frumbert/enrol-token to be installed
 * You need https://github.com/frumbert/customscript-signuptoken to be configured and installed

## Setup

* Upload the .zip file to your Moodle Site.
* Install the plugin.

### Web Services

This plugin contains a number of web service functions for creating tokens, applying tokens, verifying tokens and looking up courses and enrolments for a user.

To set up web services in this plugin:

    1. In Moodle admin, go to Plugins > Local plugins > Signup token
    2. Click on 'Open here' to show the plugin setup screen.
    2. In the 'select web service' choose 'create a new web service'
    1. Add a web service name (just a label, can be anything)
    2. Select an admin user to give the service enough permissions
    3. Press the Create web service button
    4. The page should refresh and show you the web token for accessing these functions. 

# License
© 2022 Licensed under MIT.