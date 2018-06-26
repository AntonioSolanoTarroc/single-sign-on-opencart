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
$_ ['heading_title']  = 'OneAll Single Sign-On';
$_ ['text_extension'] = 'Extensions';

$_ ['oasso_intro_message']                       = 'Please note a OneAll site with a Silver Plan is required in order to use this extension. You only need one plan to enable SSO on all of your shops.';
$_ ['oasso_api_connection_handle']               = 'API Connection Handler';
$_ ['oasso_port_field_details']                  = 'For security reasons we recommend using PHP CURL and HTTPS on port 443.';
$_ ['oasso_connection_handler_label']            = 'Connection Handler';
$_ ['oasso_connection_handled_option_curl']      = 'Use PHP CURL to communicate with the API';
$_ ['oasso_connection_handled_option_fsockopen'] = 'Use PHP FSOCKOPEN to communicate with the API';
$_ ['oasso_port_field_label']                    = 'Connection Port';
$_ ['oasso_port_field_443_label']                = 'Communication via HTTPS on port 443';
$_ ['oasso_port_field_80_label']                 = 'Communication via HTTP on port 80';
$_ ['oasso_autodetect_api_connection']           = 'Autodetect API Connection';

$_ ['oasso_api_settings_title']  = 'API Settings';
$_ ['oasso_api_settings_intro']  = 'You can create and view your API Credentials in your OneAll account. Please make sure to use the same settings in all the shops that you want to link together using SSO.';
$_ ['oasso_api_subdomain_label'] = 'API Subdomain';
$_ ['oasso_api_public_key']      = 'API Public Key';
$_ ['oasso_api_private_key']     = 'API Private Key';
$_ ['oasso_verify_api_settings'] = 'Verify API Settings';

$_ ['oasso_account_creation_title']           = 'Automatic Account Creation';
$_ ['oasso_account_creation_intro']           = 'Automatically creates new customer accounts for SSO users that do not have an account in this shop yet and logs the users in with these accounts.';
$_ ['oasso_account_creation_auto_label']      = 'Automatically create accounts?';
$_ ['oasso_account_creation_auto_option_yes'] = 'Yes, automatically create new accounts';
$_ ['oasso_account_creation_auto_option_no']  = 'No, do not create new accounts for SSO users';
$_ ['oasso_account_creation_mail_label']      = 'Send email to new customers?';
$_ ['oasso_account_creation_mail_label_yes']  = 'Yes, send an email to newly added customers';
$_ ['oasso_account_creation_mail_label_no']   = 'No, do not send an email to newly added customers';

$_ ['oasso_account_link_title']            = 'Automatic Account Link';
$_ ['oasso_account_link_intro1']           = 'Tries to automatically link SSO users to already existing customer accounts. To link accounts the email address of the SSO session is matched against the email addresses of the existing customers.';
$_ ['oasso_account_link_intro2']           = 'If the extension finds an existing account but cannot link the SSO user to it (eg. if the option is disabled), a notice reminding the user of his existing account will be displayed on the login/registration page instead.';
$_ ['oasso_account_link_label']            = 'Automatically link accounts?';
$_ ['oasso_account_link_yes']              = 'Yes, automatically link SSO users to existing accounts';
$_ ['oasso_account_link_no']               = 'No, do not link SSO users to existing accounts';
$_ ['oasso_account_link_unverified_label'] = 'Link using unverified emails?';
$_ ['oasso_account_link_unverified_yes']   = 'Yes, also use unverified email addresses to link accounts';
$_ ['oasso_account_link_unverified_no']    = 'No, do not use unverified email addresses to link accounts';
$_ ['oasso_account_link_unverified_help']  = 'Attention! For security reasons, we advise against using unverified email addresses to link accounts.';

$_ ['oasso_session_title']             = 'SSO Session Settings';
$_ ['oasso_session_lifetime_label']    = 'SSO Session Lifetime';
$_ ['oasso_session_lifetime_2_Hours']  = '2 Hours';
$_ ['oasso_session_lifetime_4_Hours']  = '4 Hours';
$_ ['oasso_session_lifetime_6_Hours']  = '6 Hours';
$_ ['oasso_session_lifetime_12_Hours'] = '12 Hours';
$_ ['oasso_session_lifetime_1_Day']    = '1 Day';
$_ ['oasso_session_lifetime_2_Days']   = '2 Days';
$_ ['oasso_session_lifetime_3_Days']   = '3 Days';
$_ ['oasso_session_lifetime_4_Days']   = '4 Days';
$_ ['oasso_session_lifetime_5_Days']   = '5 Days';
$_ ['oasso_session_lifetime_6_Days']   = '6 Days';
$_ ['oasso_session_lifetime_1_Week']   = '1 Week';
$_ ['oasso_session_lifetime_2_Weeks']  = '2 Weeks';
$_ ['oasso_session_lifetime_3_Weeks']  = '3 Weeks';
$_ ['oasso_session_lifetime_1_Month']  = '1 Month';
$_ ['oasso_session_lifetime_help']     = 'Sessions are automatically queued for deletion once their lifetime has expired.';
$_ ['oasso_session_realm_label']       = 'SSO Session Top Realm';
$_ ['oasso_session_realm_help']        = 'Optional - The primary realm of the SSO sessions generated for customers.';
$_ ['oasso_session_subrealm_label']    = 'SSO Session Sub Realm';
$_ ['oasso_session_subrealm_help']     = 'Optional - The secondary realm of the SSO sessions generated for customers.';

// user feedback
$_ ['oa_text_error_permission'] = 'You do not have required permissions.';
$_ ['oa_text_settings_saved']   = 'Your settings have been saved';

// api testing result
$_ ['oasso_text_ajax_fill_out']          = 'Please fill out each of the fields above.';
$_ ['oasso_text_ajax_wrong_handler']     = 'The connection handler does not work!';
$_ ['oasso_text_ajax_no_handler']        = 'No connection handler detected';
$_ ['oasso_text_ajax_wrong_subdomain']   = 'The API subdomain does not seem to exist!';
$_ ['oasso_text_ajax_missing_subdomain'] = 'Please fill out each of the fields above.';
$_ ['oasso_text_ajax_settings_ok']       = 'Success! The API settings are correct!';
$_ ['oasso_text_ajax_wrong_key']         = 'The API keys are invalid!';
$_ ['oasso_text_ajax_autodetect_error']  = 'Unknown result received';
$_ ['oasso_text_ajax_upgrade_your_plan'] = 'The plan you subscribe does not include the SSO service. Please, check our' . ' plans list in order to upgrade to the appropriate plan.';
$_ ['oasso_text_ajax_sso_disabled']      = 'Single Sign-On is not available for this OneAll site. Please login to your ' . ' OneAll account and upgrade the site to a higher plan in order to enable it.';

$_ ['oasso_text_ajax_curl_ok_443']      = 'Detected CURL on port 443/HTTPS';
$_ ['oasso_text_ajax_curl_ok_80']       = 'Detected CURL on port 80/HTTP';
$_ ['oasso_text_ajax_fsockopen_ok_80']  = 'Detected FSOCKOPEN on port 80/HTTP';
$_ ['oasso_text_ajax_fsockopen_ok_443'] = 'Detected FSOCKOPEN on port 443/HTTPS';
