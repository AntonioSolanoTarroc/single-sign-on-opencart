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

/**
 * Class ControllerModuleOneallsso
 */
class ControllerModuleOneallsso extends Controller
{
    /**
     *
     * @var \Oneall\Phpsdk\Client\Builder
     */
    protected $clientBuilder;

    /**
     *
     * @var \ModelExtensionEvent
     */
    protected $event_model;

    /**
     * Installer
     */
    public function install()
    {
        // User Token Storage
        $sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "oasl_user` (
                            `oasl_user_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                            `customer_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
                            `user_token` CHAR(36) COLLATE utf8_bin NOT NULL DEFAULT '',
                            `date_added` DATETIME NOT NULL,
                        PRIMARY KEY (`oasl_user_id`),
                        KEY `user_id` (`customer_id`),
                        KEY `user_token` (`user_token`));";
        $this->db->query($sql);

        // Identity Token Storage
        $sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "oasl_identity` (
                            `oasl_identity_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                            `oasl_user_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
                            `identity_token` CHAR(36) COLLATE utf8_bin NOT NULL DEFAULT '',
                            `identity_provider` VARCHAR(255) COLLATE utf8_bin NOT NULL DEFAULT '',
                            `num_logins` INT(11) NOT NULL DEFAULT '0',
                            `date_added` DATETIME NOT NULL ,
                            `date_updated` DATETIME NOT NULL,
                        PRIMARY KEY (`oasl_identity_id`),
                        UNIQUE KEY `oaid` (`oasl_identity_id`));";
        $this->db->query($sql);

        foreach ($this->getEvents() as $code => $event)
        {

            $this->getEventModel()->addEvent($code, $event ['trigger'], $event ['action']);
        }
    }

    /**
     * Uninstaller
     */
    public function uninstall()
    {
        // Removes tables
        // These table should normally not be dropped, otherwise the customers can no longer
        // login if the webmaster re-installs the extension.

        // These table are alos shared with the OneAll Social-Login login plugin
        // $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "oasl_user`;");
        // $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "oasl_identity`;");

        // removing subscribed event.
        foreach ($this->getEvents() as $code => $event)
        {
            $this->getEventModel()->deleteEvent($code);
        }
    }

    /**
     * Display Admin
     */
    public function index()
    {
        $this->install();

        $this->setupView();
        $data = $this->loadData();

        // What do we need to do?
        $do = (!empty ($this->request->get ['do']) ? $this->request->get ['do'] : 'settings');

        // Autodetect API Communication Settings
        if ($do == 'autodetect_api_connection')
        {
            $this->autodetect_api_connection($data);
        }
        // Verify API Settings
        elseif ($do == 'verify_api_settings')
        {
            $this->verify_api_settings($data);
        }

        if (($this->request->server ['REQUEST_METHOD'] == 'POST') && $this->validate())
        {
            $form                = new oasso_form_handler ();
            $this->request->post = $form->map($this->request->post);

            $this->model_setting_setting->editSetting('oasso', $this->request->post);

            // Redirect
            $args = ('token=' . $this->session->data ['token'] . '&oa_action=saved');
            $this->response->redirect($this->url->link('module/oneallsso', $args, true));
        }

        // Settings Saved
        if (isset ($this->request->get) && !empty ($this->request->get ['oa_action']) == 'saved')
        {
            $data ['oa_success_message'] = $data ['oa_text_settings_saved'];
        }

        // Error Message
        if (!empty ($this->error ['warning']))
        {
            $data ['oa_error_message'] = $this->error ['warning'];
        }

        // Display Page
        $this->response->setOutput($this->load->view('module/oneallsso.tpl', $data));
    }

    /**
     * Returns default config values.
     *
     * @return array
     */
    public function get_default_values()
    {
        $values = [
            'oasso_handler' => 'curl',
            'oasso_port' => 443,
            'oasso_accounts_create_auto' => 1,
            'oasso_accounts_create_sendmail' => 1,
            'oasso_accounts_link_automatic' => 1,
            'oasso_accounts_link_unverified' => 0,
            'oasso_session_lifetime' => 7200,
            'oasso_public_key' => '',
            'oasso_private_key' => '',
            'oasso_session_realm' => '',
            'oasso_session_subrealm' => '',
            'oasso_api_subdomain' => ''
        ];

        return $values;
    }

    /**
     * Return view base element
     *
     * @return array
     */
    protected function setupView()
    {
        // CSS & JS
        $this->document->addStyle('view/stylesheet/oneallsso/backend.css');
        $this->document->addScript('view/javascript/oneallsso/backend.js');

        // Load Models
        $this->load->model('setting/setting');
        $this->load->model('design/layout');
    }

    /**
     * Load all data to display (template, language, model values, ...)
     *
     * @return array
     */
    protected function loadData()
    {
        $data = $this->get_default_values();

        // language
        $data = array_merge($data, $this->load->language('module/oneallsso'));

        $token = $this->session->data ['token'];

        // BreadCrumbs
        $data ['breadcrumbs'] = array(
            array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/dashboard', 'token=' . $token, true),
                'separator' => false
            ),
            array(
                'text' => $this->language->get('text_extension'),
                'href' => $this->url->link('extension/extension', 'token=' . $token, true),
                'separator' => ' :: '
            ),
            array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('module/oneallsso', 'token=' . $token, true),
                'separator' => ' :: '
            )
        );

        // Page Title
        $this->document->setTitle($this->language->get('heading_title'));

        // Buttons
        $data ['action'] = $this->url->link('module/oneallsso', 'token=' . $token, true);
        $data ['cancel'] = $this->url->link('module/oneallsso', 'token=' . $token, true);

        // Template
        $data ['header']      = $this->load->controller('common/header');
        $data ['column_left'] = $this->load->controller('common/column_left');
        $data ['footer']      = $this->load->controller('common/footer');

        // Model
        $model = $this->model_setting_setting->getSetting('oasso');
        $data  = array_merge($data, $model);

        return $data;
    }

    /**
     * Ensure the user has required permissions
     *
     * @return bool
     */
    private function validate()
    {
        // Can this user modify the settings?
        if (!$this->user->hasPermission('modify', 'module/oneallsso'))
        {
            $this->error ['warning'] = $this->language->get('oa_text_error_permission');

            return false;
        }

        // Done
        return true;
    }

    /**
     * Check API Settings
     *
     * @param string $lang
     */
    public function verify_api_settings($lang)
    {
        // Read arguments.
        $get = (is_array($this->request->get) ? $this->request->get : array());

        // Parse arguments
        $oneall_subdomain   = (!empty ($get ['oneall_subdomain']) ? trim($get ['oneall_subdomain']) : '');
        $oneall_public      = (!empty ($get ['oneall_public']) ? trim($get ['oneall_public']) : '');
        $oneall_private     = (!empty ($get ['oneall_private']) ? trim($get ['oneall_private']) : '');
        $oneall_api_handler = (!empty ($get ['oneall_api_handler']) ? trim($get ['oneall_api_handler']) : '');
        $oneall_api_port    = (!empty ($get ['oneall_api_port']) ? trim($get ['oneall_api_port']) : '');
        $isSecure           = ($oneall_api_port == 443 ? true : false);

        // Check if all fields have been filled out.
        if (strlen($oneall_subdomain) == 0 || strlen($oneall_public) == 0 || strlen($oneall_private) == 0)
        {
            $this->sendResponse($lang ['oasso_text_ajax_fill_out']);
        }

        // The full domain has been entered.
        if (preg_match("/([a-z0-9\-]+)\.api\.oneall\.com/i", $oneall_subdomain, $matches))
        {
            $oneall_subdomain = $matches [1];
        }

        // Check format of the subdomain.
        if (!preg_match("/^[a-z0-9\-]+$/i", $oneall_subdomain))
        {
            $this->sendResponse($lang ['oasso_text_ajax_wrong_subdomain']);
        }

        //
        // Try to establish a connection.
        //
        $client = $this->getBuilder()->build($oneall_api_handler, $oneall_subdomain, $oneall_public, $oneall_private,
                                             $isSecure, 'api.oneall.loc')
        ;

        // ensure function is working through a test
        $status = false;
        $result = null;

        try
        {
            $result = $client->get('/site.json');
        }
        catch (\Exception $exception)
        {
            $this->sendResponse($lang ['oasso_text_ajax_autodetect_error']);
        }

        if (!$result instanceof \Oneall\Phpsdk\Client\Response)
        {
            $this->sendResponse($lang ['oasso_text_ajax_autodetect_error']);
        }

        switch ($result->getStatusCode())
        {
            // Connection successful.
            case 200 :
                $body = json_decode($result->getBody());
                if (empty ($body->response->result->data->site->subscription_plan->features->has_single_signon))
                {
                    $status_message = $lang ['oasso_text_ajax_upgrade_your_plan'];
                    $status         = false;
                    break;
                }
                $status_message = $lang ['oasso_text_ajax_settings_ok'];
                $status         = true;
                break;

            // Authentication Error.
            case 401 :
                $status_message = $lang ['oasso_text_ajax_wrong_key'];
                break;

            // Authentication Error.
            case 403 :
                $status_message = $lang ['oasso_text_ajax_sso_disabled'];
                break;

            // Wrong Subdomain.
            case 404 :
                $status_message = $lang ['oasso_text_ajax_wrong_subdomain'];
                break;

            // Other error.
            default :
                $status_message = $lang ['oasso_text_ajax_autodetect_error'];

                break;
        }

        $this->sendResponse($status_message, $status);
    }

    /**
     * Automatic API Detection
     *
     * @param
     *            $lang
     */
    public function autodetect_api_connection($lang)
    {
        $status = 'success';

        // Check CURL HTTPS - Port 443.
        if ($this->check_curl(true) === true)
        {
            $status_message = $lang ['oasso_text_ajax_curl_ok_443'];
            $handler        = 'curl';
            $port           = '443';
        }
        // Check FSOCKOPEN HTTPS - Port 443.
        elseif ($this->check_fsockopen(true) == true)
        {
            $status_message = $lang ['oasso_text_ajax_fsockopen_ok_443'];
            $handler        = 'fsockopen';
            $port           = '443';
        }
        // Check CURL HTTP - Port 80.
        elseif ($this->check_curl(false) === true)
        {
            $status_message = $lang ['oasso_text_ajax_curl_ok_80'];
            $handler        = 'curl';
            $port           = '80';
        }
        // Check FSOCKOPEN HTTP - Port 80.
        elseif ($this->check_fsockopen(false) == true)
        {
            $status_message = '|' . $lang ['oasso_text_ajax_fsockopen_ok_80'];
            $handler        = 'fsockopen';
            $port           = '80';
        }
        // No working handler found.
        else
        {
            $status         = 'error';
            $status_message = $lang ['oasso_text_ajax_no_handler'];
            $handler        = null;
            $port           = null;
        }

        $response = [
            'port' => $port,
            'handler' => $handler,
            'status' => $status,
            'status_message' => $status_message
        ];
        // Output for AJAX.
        die (json_encode($response));
    }

    // Returns a list of disabled PHP functions.
    private function get_php_disabled_functions()
    {
        $disabled_functions = trim(ini_get('disable_functions'));
        if (strlen($disabled_functions) == 0)
        {
            $disabled_functions = array();
        }
        else
        {
            $disabled_functions = explode(',', $disabled_functions);
            $disabled_functions = array_map('trim', $disabled_functions);
        }

        return $disabled_functions;
    }

    /**
     * Checks if CURL can be used.
     *
     * @param bool $secure
     *
     * @return bool
     */
    private function check_curl($secure = true)
    {
        return $this->checkConnectivity('curl', $secure, 'curl', 'curl_exec');
    }

    /**
     * Checks if fsockopen can be used.
     *
     * @param bool $secure
     *
     * @return bool
     */
    private function check_fsockopen($secure = true)
    {
        return $this->checkConnectivity('fsockopen', $secure, null, 'fsockopen');
    }

    /**
     *
     * @param string  $handler
     * @param boolean $isSecure
     * @param string  $extension
     * @param string  $function
     *
     * @return bool
     */
    private function checkConnectivity($handler, $isSecure, $extension, $function)
    {
        // check whether the extensions is loaded
        if (!in_array($extension, get_loaded_extensions()))
        {
            return false;
        }

        // check whether the curlexec function exists and availble
        if (!function_exists($function) || in_array($function, $this->get_php_disabled_functions()))
        {
            return false;
        }

        $client = $this->getBuilder()->build($handler, 'www', '', '', $isSecure, 'oneall.loc');

        // ensure function is working through a test
        $result = $client->get('/ping.html');
        if (!$result || $result->getBody() != 'OK' || $result->getStatusCode() != 200)
        {
            return false;
        }

        return true;
    }

    /**
     *
     * @return \ModelExtensionEvent
     */
    private function getEventModel()
    {
        // Callback Handler
        if (defined('VERSION') && version_compare(VERSION, '2.2.0', '>=') && !$this->model_extension_event instanceof \ModelExtensionEvent)
        {
            $this->load->model('extension/event');
        }

        return $this->model_extension_event;
    }

    /**
     * Returns events configuration list used by the module
     *
     * @return array
     */
    private function getEvents()
    {
        $events = array(
            'oneall_before_update' => [
                'trigger' => 'catalog/controller/account/edit/before',
                'action' => 'module/oneallssoupdate/preUpdate'
            ],
            'oneall_before_password' => [
                'trigger' => 'catalog/controller/account/password/before',
                'action' => 'module/oneallssoupdate/prePasswordUpdate'
            ],
            'oneall_after_password' => [
                'trigger' => 'catalog/controller/account/account/after',
                'action' => 'module/oneallssoupdate/postPasswordUpdate'
            ],
            'oneall_after_update' => [
                'trigger' => 'catalog/controller/account/account/after',
                'action' => 'module/oneallssoupdate/postUpdate'
            ],
            'oneall_before_register' => [
                'trigger' => 'catalog/controller/account/register/before',
                'action' => 'module/oneallssoregister/preRegister'
            ],
            'oneall_after_register' => [
                'trigger' => 'catalog/controller/account/success/before',
                'action' => 'module/oneallssoregister/postRegister'
            ],
            'oneall_before_logout' => [
                'trigger' => 'catalog/controller/account/logout/before',
                'action' => 'module/oneallssologin/preLogout'
            ],
            'oneall_before_login' => [
                'trigger' => 'catalog/controller/account/login/before',
                'action' => 'module/oneallssologin/preLogin'
            ],
            'oneall_after_login' => [
                'trigger' => 'catalog/controller/account/account/before',
                'action' => 'module/oneallssologin/postLogin'
            ],
            'oneall_connect_sso' => [
                'trigger' => 'catalog/controller/common/header/before',
                'action' => 'module/oneallssocallback/callback'
            ]
        );

        return $events;
    }

    /**
     *
     * @return \Oneall\Phpsdk\Client\Builder
     */
    private function getBuilder()
    {
        if (!$this->clientBuilder)
        {
            $this->clientBuilder = new \Oneall\Phpsdk\Client\Builder ();
        }

        return $this->clientBuilder;
    }

    /**
     * Build json response for ajax communcation
     *
     * @param string $message
     * @param bool   $status
     *
     * @return string
     */
    private function sendResponse($message, $status = false)
    {
        $response = [
            'status' => $status ? 'success' : 'error',
            'status_message' => $message
        ];

        die (json_encode($response));
    }
}

/**
 * Class oasso_form_handler
 */
class oasso_form_handler
{

    /**
     * Handle & validate submitted data.
     *
     * @param array $post
     *
     * @return array
     */
    public function map(array $post)
    {
        $hour          = 3600;
        $day           = (3600 * 24);
        $lifetime_list = [
            $hour * 2,
            $hour * 4,
            $hour * 6,
            $hour * 12,
            $day,
            $day * 2,
            $day * 3,
            $day * 4,
            $day * 5,
            $day * 6,
            $day * 7,
            $day * 14,
            $day * 21,
            $day * 28
        ];

        $values ['oasso_handler']                  = $this->get($post, 'oasso_handler', ['curl', 'fsockopen'], 'curl');
        $values ['oasso_port']                     = $this->get($post, 'oasso_port', [443, 80], 443);
        $values ['oasso_accounts_create_auto']     = $this->get($post, 'oasso_accounts_create_auto', [0, 1], 1);
        $values ['oasso_accounts_create_sendmail'] = $this->get($post, 'oasso_accounts_create_sendmail', [0, 1], 1);
        $values ['oasso_accounts_link_automatic']  = $this->get($post, 'oasso_accounts_link_automatic', [0, 1], 1);
        $values ['oasso_accounts_link_unverified'] = $this->get($post, 'oasso_accounts_link_unverified', [0, 1], 0);
        $values ['oasso_session_lifetime']         = $this->get($post, 'oasso_session_lifetime', $lifetime_list, 7200);
        $values ['oasso_public_key']               = $this->get($post, 'oasso_public_key', [], '');
        $values ['oasso_private_key']              = $this->get($post, 'oasso_private_key', [], '');
        $values ['oasso_session_realm']            = $this->get($post, 'oasso_session_realm', [], '');
        $values ['oasso_session_subrealm']         = $this->get($post, 'oasso_session_subrealm', [], '');
        $values ['oasso_api_subdomain']            = $this->handle_subdomain($post, 'oasso_api_subdomain');

        return $values;
    }

    /**
     * Extract subdomain from given $post array
     *
     * @param array  $post
     * @param string $field
     *
     * @return string
     */
    protected function handle_subdomain(array $post, $field)
    {
        if (empty ($post [$field]))
        {
            return '';
        }

        $subdomain = trim($post [$field]);

        // The full domain has been entered.
        if (preg_match("/([a-z0-9\-]+)\.api\.oneall\.com/i", $subdomain, $matches))
        {
            $subdomain = $matches [1];
        }

        return $subdomain;
    }

    /**
     * Get value from submitted data.
     *
     * @param array $data
     * @param
     *            $field
     * @param array $restriction_list
     * @param
     *            $default
     *
     * @return mixed
     */
    protected function get(array $data, $field, array $restriction_list, $default)
    {
        if (!array_key_exists($field, $data))
        {
            return $default;
        }

        $value = $data [$field];

        // check if the given value is in the restriction list.
        if (!empty ($restriction_list) && !in_array($value, $restriction_list))
        {
            return $default;
        }

        return $value;
    }
}
