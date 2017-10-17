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
$_ ['site_base'] = HTTP_SERVER;
$_ ['site_ssl']  = HTTPS_SERVER;

// Database
$_ ['db_autostart'] = true;
$_ ['db_type']      = DB_DRIVER; // mpdo, mssql, mysql, mysqli or postgre
$_ ['db_hostname']  = DB_HOSTNAME;
$_ ['db_username']  = DB_USERNAME;
$_ ['db_password']  = DB_PASSWORD;
$_ ['db_database']  = DB_DATABASE;
$_ ['db_port']      = DB_PORT;

// Session
$_ ['session_autostart'] = true;

// Actions
$_ ['action_pre_action'] = array(
    'startup/startup',
    'startup/error',
    'startup/event',
    'startup/sass',
    'startup/login',
    'startup/permission'
);

// Actions
$_ ['action_default'] = 'common/dashboard';

// Action Events
$_ ['action_event'] = array(
    'view/*/before' => 'event/theme',
    'controller/extension/analytics/*/before' => 'event/compatibility/controller',
    'controller/extension/captcha/*/before' => 'event/compatibility/controller',
    'controller/extension/feed/*/before' => 'event/compatibility/controller',
    'controller/extension/fraud/*/before' => 'event/compatibility/controller',
    'controller/extension/module/*/before' => 'event/compatibility/controller',
    'controller/extension/payment/*/before' => 'event/compatibility/controller',
    'controller/extension/recurring/*/before' => 'event/compatibility/controller',
    'controller/extension/shipping/*/before' => 'event/compatibility/controller',
    'controller/extension/theme/*/before' => 'event/compatibility/controller',
    'controller/extension/total/*/before' => 'event/compatibility/controller',
    'view/analytics/*/before' => 'event/compatibility/view',
    'view/captcha/*/before' => 'event/compatibility/view',
    'view/feed/*/before' => 'event/compatibility/view',
    'view/fraud/*/before' => 'event/compatibility/view',
    'view/module/*/before' => 'event/compatibility/view',
    'view/payment/*/before' => 'event/compatibility/view',
    'view/recurring/*/before' => 'event/compatibility/view',
    'view/shipping/*/before' => 'event/compatibility/view',
    'view/theme/*/before' => 'event/compatibility/view',
    'view/total/*/before' => 'event/compatibility/view',
    'language/extension/analytics/*/before' => 'event/compatibility/language',
    'language/extension/captcha/*/before' => 'event/compatibility/language',
    'language/extension/feed/*/before' => 'event/compatibility/language',
    'language/extension/fraud/*/before' => 'event/compatibility/language',
    'language/extension/module/*/before' => 'event/compatibility/language',
    'language/extension/payment/*/before' => 'event/compatibility/language',
    'language/extension/recurring/*/before' => 'event/compatibility/language',
    'language/extension/shipping/*/before' => 'event/compatibility/language',
    'language/extension/theme/*/before' => 'event/compatibility/language',
    'language/extension/total/*/before' => 'event/compatibility/language'
);
