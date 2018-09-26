<?php

/**
 * @package      Oneall Single Sign-On
 * @copyright    Copyright 2017-Present http://www.oneall.com
 * @license      GNU/GPL 2 or later
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307,USA.
 *
 * The "GNU General Public License" (GPL) is available at
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 */

// ////////////////////////////////////////////////////////////////////
// Single Sign-On Admin
// ////////////////////////////////////////////////////////////////////
$_['heading_title'] = 'OneAll Single Sign-On';
$_['text_extension'] = 'Extensions';

$_['oneallsso_intro_message'] = 'Please note a OneAll site with a Silver Plan is required in order to use this extension. You only need one plan to enable SSO on all of your shops.';
$_['oneallsso_api_connection_settings'] = 'API Connection Handler';
$_['oneallsso_port_field_details'] = 'For security reasons we recommend using PHP CURL and HTTPS on port 443. Use the Autodetect button to set the best available configuration.';
$_['oneallsso_api_handler_label'] = 'Connection Handler';
$_['oneallsso_connection_handled_option_curl'] = 'Use PHP CURL to communicate with the API';
$_['oneallsso_connection_handled_option_fsockopen'] = 'Use PHP FSOCKOPEN to communicate with the API';
$_['oneallsso_api_port_label'] = 'Connection Port';
$_['oneallsso_api_port_443_label'] = 'Communication via HTTPS on port 443';
$_['oneallsso_api_port_80_label'] = 'Communication via HTTP on port 80';
$_['oneallsso_autodetect_api_connection'] = 'Autodetect API Connection';
$_['oneallsso_plugin_status'] = 'Module Status';
$_['oneallsso_plugin_enabled'] = 'Enabled';
$_['oneallsso_plugin_disabled'] = 'Disabled';

$_['oneallsso_api_settings_title'] = 'API Settings';
$_['oneallsso_api_settings_intro'] = 'Make sure to use the same settings in all the shops that you want to link together using SSO. Use the Verify button to check if everything is setup correctly.';
$_['oneallsso_api_subdomain_label'] = 'API Subdomain';
$_['oneallsso_api_public_key_label'] = 'API Public Key';
$_['oneallsso_api_private_key_label'] = 'API Private Key';
$_['oneallsso_verify_api_settings'] = 'Verify API Settings';
$_['oneallsso_my_account'] = 'Create or view API Credentials';

$_['oneallsso_account_creation_title'] = 'Automatic Account Creation';
$_['oneallsso_account_creation_intro'] = 'Automatically creates new customer accounts for SSO users that do not have an account in this shop yet and logs the users in with these accounts.';
$_['oneallsso_account_creation_auto_label'] = 'Automatically create accounts?';
$_['oneallsso_account_creation_auto_option_yes'] = 'Yes, automatically create new accounts';
$_['oneallsso_account_creation_auto_option_no'] = 'No, do not create new accounts for SSO users';
$_['oneallsso_account_creation_mail_label'] = 'Send email to new customers?';
$_['oneallsso_account_creation_mail_label_yes'] = 'Yes, send an email to newly added customers';
$_['oneallsso_account_creation_mail_label_no'] = 'No, do not send an email to newly added customers';

$_['oneallsso_account_link_title'] = 'Automatic Account Link';
$_['oneallsso_account_link_intro1'] = 'Tries to automatically link SSO users to already existing customer accounts. To link accounts the email address of the SSO session is matched against the email addresses of the existing customers.';
$_['oneallsso_account_link_intro2'] = 'If the extension finds an existing account but cannot link the SSO user to it (eg. if the option is disabled), a notice reminding the user of his existing account will be displayed on the login/registration page instead.';
$_['oneallsso_account_link_label'] = 'Automatically link accounts?';
$_['oneallsso_account_link_yes'] = 'Yes, automatically link SSO users to existing accounts';
$_['oneallsso_account_link_no'] = 'No, do not link SSO users to existing accounts';
$_['oneallsso_account_link_unverified_label'] = 'Link using unverified emails?';
$_['oneallsso_account_link_unverified_yes'] = 'Yes, also use unverified email addresses to link accounts';
$_['oneallsso_account_link_unverified_no'] = 'No, do not use unverified email addresses to link accounts';
$_['oneallsso_account_link_unverified_help'] = 'Attention! For security reasons, we advise against using unverified email addresses to link accounts.';

$_['oneallsso_session_title'] = 'SSO Session Settings';
$_['oneallsso_session_lifetime_label'] = 'SSO Session Lifetime';
$_['oneallsso_session_lifetime_2_Hours'] = '2 Hours';
$_['oneallsso_session_lifetime_4_Hours'] = '4 Hours';
$_['oneallsso_session_lifetime_6_Hours'] = '6 Hours';
$_['oneallsso_session_lifetime_12_Hours'] = '12 Hours';
$_['oneallsso_session_lifetime_1_Day'] = '1 Day';
$_['oneallsso_session_lifetime_2_Days'] = '2 Days';
$_['oneallsso_session_lifetime_3_Days'] = '3 Days';
$_['oneallsso_session_lifetime_4_Days'] = '4 Days';
$_['oneallsso_session_lifetime_5_Days'] = '5 Days';
$_['oneallsso_session_lifetime_6_Days'] = '6 Days';
$_['oneallsso_session_lifetime_1_Week'] = '1 Week';
$_['oneallsso_session_lifetime_2_Weeks'] = '2 Weeks';
$_['oneallsso_session_lifetime_3_Weeks'] = '3 Weeks';
$_['oneallsso_session_lifetime_1_Month'] = '1 Month';
$_['oneallsso_session_lifetime_help'] = 'Sessions are automatically queued for deletion once their lifetime has expired.';
$_['oneallsso_session_realm_label'] = 'SSO Session Top Realm';
$_['oneallsso_session_realm_help'] = 'Optional - The primary realm of the SSO sessions generated for customers.';
$_['oneallsso_session_subrealm_label'] = 'SSO Session Sub Realm';
$_['oneallsso_session_subrealm_help'] = 'Optional - The secondary realm of the SSO sessions generated for customers.';

// user feedback
$_['oneallsso_error_permission'] = 'You do not have required permissions.';
$_['oneallsso_settings_saved'] = 'Your settings have been saved';

// api testing result
$_['oneallsso_text_ajax_fill_out'] = 'Please fill out each of the fields above.';
$_['oneallsso_text_ajax_wrong_handler'] = 'The connection handler does not work!';
$_['oneallsso_text_ajax_no_handler'] = 'No connection handler detected';
$_['oneallsso_text_ajax_wrong_subdomain'] = 'The API subdomain does not seem to exist!';
$_['oneallsso_text_ajax_missing_subdomain'] = 'Please fill out each of the fields above.';
$_['oneallsso_text_ajax_settings_ok'] = 'Success! The API settings are correct!';
$_['oneallsso_text_ajax_wrong_key'] = 'The API keys are invalid!';
$_['oneallsso_text_ajax_autodetect_error'] = 'Unknown result received';
$_['oneallsso_text_ajax_upgrade_your_plan'] = 'The subscription plan of that site does not include the SSO service. Please upgrade your subscription to a plan that includes SSO.';
$_['oneallsso_text_ajax_sso_disabled'] = 'Single Sign-On is not available for this OneAll site. Please login to your ' . ' OneAll account and upgrade the site to a higher plan in order to enable it.';

$_['oneallsso_text_ajax_curl_ok_443'] = 'Detected CURL on port 443/HTTPS';
$_['oneallsso_text_ajax_curl_ok_80'] = 'Detected CURL on port 80/HTTP';
$_['oneallsso_text_ajax_fsockopen_ok_80'] = 'Detected FSOCKOPEN on port 80/HTTP';
$_['oneallsso_text_ajax_fsockopen_ok_443'] = 'Detected FSOCKOPEN on port 443/HTTPS';
