<?php

/**
 * @package      OneAll SDK
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

// Site
$_ ['site_base'] = '';
$_ ['site_ssl']  = false;

// Url
$_ ['url_autostart'] = true;

// Language
$_ ['language_default']  = 'en-gb';
$_ ['language_autoload'] = array(
    'en-gb'
);

// Database
$_ ['db_autostart'] = false;
$_ ['db_type']      = 'mysqli'; // mpdo, mssql, mysql, mysqli or postgre
$_ ['db_hostname']  = 'localhost';
$_ ['db_username']  = 'root';
$_ ['db_password']  = '';
$_ ['db_database']  = '';
$_ ['db_port']      = 3306;

// Mail
$_ ['mail_protocol']      = 'mail'; // mail or smtp
$_ ['mail_from']          = ''; // Your E-Mail
$_ ['mail_sender']        = ''; // Your name or company name
$_ ['mail_reply_to']      = ''; // Reply to E-Mail
$_ ['mail_smtp_hostname'] = '';
$_ ['mail_smtp_username'] = '';
$_ ['mail_smtp_password'] = '';
$_ ['mail_smtp_port']     = 25;
$_ ['mail_smtp_timeout']  = 5;
$_ ['mail_verp']          = false;
$_ ['mail_parameter']     = '';

// Cache
$_ ['cache_type']   = 'file'; // apc, file or mem
$_ ['cache_expire'] = 3600;

// Session
$_ ['session_autostart'] = true;
$_ ['session_name']      = 'PHPSESSID';

// Template
$_ ['template_type'] = 'php';

// Error
$_ ['config_error_display']  = true;
$_ ['config_error_log']      = true;
$_ ['config_error_filename'] = 'error.log';

// Reponse
$_ ['response_header']      = array(
    'Content-Type: text/html; charset=utf-8'
);
$_ ['response_compression'] = 0;

// Autoload Configs
$_ ['config_autoload'] = array();

// Autoload Libraries
$_ ['library_autoload'] = array();

// Autoload Libraries
$_ ['model_autoload'] = array();

// Actions
$_ ['action_default']    = 'common/home';
$_ ['action_router']     = 'startup/router';
$_ ['action_error']      = 'error/not_found';
$_ ['action_pre_action'] = array();
$_ ['action_event']      = array();
