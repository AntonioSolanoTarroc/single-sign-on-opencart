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
 * Class ControllerExtensionModuleOneallsso
 */
class ControllerExtensionModuleOneallsso extends Controller
{
    /**
     *
     * @var \Oneall\Phpsdk\Client\Builder
     */
    protected $clientBuilder;

    /**
     * Installer.
     */
    public function install()
    {
        // User Token Storage.
        $sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "oasl_user` (
                `oasl_user_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `customer_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
                `user_token` CHAR(36) COLLATE utf8_bin NOT NULL DEFAULT '',
                `date_added` DATETIME NOT NULL,
                PRIMARY KEY (`oasl_user_id`),
                KEY `user_id` (`customer_id`),
                KEY `user_token` (`user_token`));";
        $this->db->query($sql);

        // Identity Token Storage.
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

        // Update events.
        $this->set_events();
    }

    /**
     * Uninstaller.
     */
    public function uninstall()
    {
        // The table should normally not be dropped, otherwise the customers can no longer login if the webmaster re-installs the extension.
        // $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "oasl_user`;");
        // $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "oasl_identity`;");

        // Remove events.
        $this->unset_events();
    }

    // Sanitizes form data.
    public function sanitize($data)
    {
        // Remove spaces.
        $data = array_map('trim', $data);

        // API Subdomain.
        if (isset($data['module_oneallsso_api_subdomain']))
        {
            // The full domain has been entered.
            if (preg_match("/([a-z0-9\-]+)\.api\.oneall\.com/i", $data['module_oneallsso_api_subdomain'], $matches))
            {
                $data['module_oneallsso_api_subdomain'] = $matches[1];
            }
        }

        // Done.
        return $data;
    }

    // Admin features.
    public function index()
    {
        // Load Models.
        $this->load->model('setting/setting');
        $this->load->model('design/layout');

        // Language.
        $data = $this->load->language('extension/module/oneallsso');

        // What do we need to do?
        $do = (!empty($this->request->get['do']) ? $this->request->get['do'] : 'settings');

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
        else
        {
            // CSS & JS.
            $this->document->addStyle('view/stylesheet/oneallsso/backend.css');
            $this->document->addScript('view/javascript/oneallsso/backend.js');

            // Save settings.
            if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate())
            {
                // Parse data.
                $form_data = $this->sanitize($this->request->post);

                // Create unique key.
                if (is_null($this->config->get('module_oneallsso_uniqid')))
                {
                    $form_data['module_oneallsso_uniqid'] = $this->generate_uniqid();
                }
                else
                {
                    $form_data['module_oneallsso_uniqid'] = $this->config->get('module_oneallsso_uniqid');
                }

                // Save.
                $this->model_setting_setting->editSetting('module_oneallsso', $form_data);

                // Update events.
                $this->set_events();

                // Redirect.
                $this->response->redirect($this->url->link('extension/module/oneallsso', 'user_token=' . $this->session->data['user_token'] . '&type=module&status=saved', true));
            }

            // Status
            if (!is_null($this->config->get('module_oneallsso_status')))
            {
                $data['oneallsso_status'] = $this->config->get('module_oneallsso_status');
            }
            else
            {
                $data['oneallsso_status'] = 0;
            }

            // API Connection Handler.
            if (!is_null($this->config->get('module_oneallsso_api_handler')))
            {
                $data['oneallsso_api_handler'] = $this->config->get('module_oneallsso_api_handler');
            }
            else
            {
                $data['oneallsso_api_handler'] = 'curl';
            }

            // API Connection Port.
            if (!is_null($this->config->get('module_oneallsso_api_port')))
            {
                $data['oneallsso_api_port'] = $this->config->get('module_oneallsso_api_port');
            }
            else
            {
                $data['oneallsso_api_port'] = '443';
            }

            // API Subdomain.
            if (!is_null($this->config->get('module_oneallsso_api_subdomain')))
            {
                $data['oneallsso_api_subdomain'] = $this->config->get('module_oneallsso_api_subdomain');
            }
            else
            {
                $data['oneallsso_api_subdomain'] = '';
            }

            // API Public Key.
            if (!is_null($this->config->get('module_oneallsso_api_public_key')))
            {
                $data['oneallsso_api_public_key'] = $this->config->get('module_oneallsso_api_public_key');
            }
            else
            {
                $data['oneallsso_api_public_key'] = '';
            }

            // API Private Key.
            if (!is_null($this->config->get('module_oneallsso_api_private_key')))
            {
                $data['oneallsso_api_private_key'] = $this->config->get('module_oneallsso_api_private_key');
            }
            else
            {
                $data['oneallsso_api_private_key'] = '';
            }

            // Automatically create accounts?
            if (!is_null($this->config->get('module_oneallsso_accounts_create_auto')))
            {
                $data['oneallsso_accounts_create_auto'] = $this->config->get('module_oneallsso_accounts_create_auto');
            }
            else
            {
                $data['oneallsso_accounts_create_auto'] = 1;
            }

            // Send email to new customers?
            if (!is_null($this->config->get('module_oneallsso_accounts_create_sendmail')))
            {
                $data['oneallsso_accounts_create_sendmail'] = $this->config->get('module_oneallsso_accounts_create_sendmail');
            }
            else
            {
                $data['oneallsso_accounts_create_sendmail'] = 1;
            }

            // Automatically link accounts?
            if (!is_null($this->config->get('module_oneallsso_accounts_link_automatic')))
            {
                $data['oneallsso_accounts_link_automatic'] = $this->config->get('module_oneallsso_accounts_link_automatic');
            }
            else
            {
                $data['oneallsso_accounts_link_automatic'] = 1;
            }

            // Link using unverified emails?
            if (!is_null($this->config->get('module_oneallsso_accounts_link_unverified')))
            {
                $data['oneallsso_accounts_link_unverified'] = $this->config->get('module_oneallsso_accounts_link_unverified');
            }
            else
            {
                $data['oneallsso_accounts_link_unverified'] = 0;
            }

            // SSO Session Lifetime.
            if (!is_null($this->config->get('module_oneallsso_session_lifetime')))
            {
                $data['oneallsso_session_lifetime'] = $this->config->get('module_oneallsso_session_lifetime');
            }
            else
            {
                $data['oneallsso_session_lifetime'] = 21600;
            }

            // SSO Session Top Realm.
            if (!is_null($this->config->get('module_oneallsso_session_realm')))
            {
                $data['oneallsso_session_realm'] = $this->config->get('module_oneallsso_session_realm');
            }
            else
            {
                $data['oneallsso_session_realm'] = '';
            }

            // SSO Session Sub Realm.
            if (!is_null($this->config->get('module_oneallsso_session_subrealm')))
            {
                $data['oneallsso_session_subrealm'] = $this->config->get('module_oneallsso_session_subrealm');
            }
            else
            {
                $data['oneallsso_session_subrealm'] = '';
            }

            // Page Title.
            $this->document->setTitle($this->language->get('heading_title'));

            // BreadCrumbs.
            $data['breadcrumbs'] = array(
                array(
                    'text' => $this->language->get('text_home'),
                    'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
                ),
                array(
                    'text' => $this->language->get('text_extension'),
                    'href' => $this->url->link('extension/extension', 'user_token=' . $this->session->data['user_token'], '&type=module', true)
                ),
                array(
                    'text' => $this->language->get('heading_title'),
                    'href' => $this->url->link('extension/module/oneallsso', 'user_token=' . $this->session->data['user_token'], true)
                )
            );

            // Buttons.
            $data['action'] = $this->url->link('extension/module/oneallsso', 'user_token=' . $this->session->data['user_token'], true);
            $data['cancel'] = $this->url->link('extension/module/oneallsso', 'user_token=' . $this->session->data['user_token'], true);
            $data['user_token'] = $this->session->data['user_token'];

            // Template.
            $data['header'] = $this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');

            // Success Message.
            if (isset($this->request->get['status']) && $this->request->get['status'] == 'saved')
            {
                $data['oneallsso_success'] = $data['oneallsso_settings_saved'];
            }

            // Error Message.
            if (!empty($this->error['warning']))
            {
                $data['oneallsso_error'] = $this->error['warning'];
            }
        }

        // Display Page.
        $this->response->setOutput($this->load->view('extension/module/oneallsso', $data));
    }

    /**
     * Generates a unique id.
     *
     * @return string
     */
    private function generate_uniqid($length = 5)
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for($i = 0; $i < $length; $i ++)
        {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * Make sure the user has the required permissions.
     *
     * @return bool
     */
    private function validate()
    {
        // Can this user modify the settings?
        if (!$this->user->hasPermission('modify', 'extension/module/oneallsso'))
        {
            $this->error['warning'] = $this->language->get('oneallsso_error_permission');
            return false;
        }

        // Done
        return true;
    }

    /**
     * Check the OneAll API Settings.
     *
     * @param string $lang
     */
    private function verify_api_settings($lang)
    {
        // Read arguments.
        $get = (is_array($this->request->get) ? $this->request->get : array());

        // Parse arguments
        $api_subdomain = (!empty($get['oneallsso_api_subdomain']) ? trim($get['oneallsso_api_subdomain']) : '');
        $api_public_key = (!empty($get['oneallsso_api_public_key']) ? trim($get['oneallsso_api_public_key']) : '');
        $api_private_key = (!empty($get['oneallsso_api_private_key']) ? trim($get['oneallsso_api_private_key']) : '');
        $api_handler = (!empty($get['oneallsso_api_handler']) ? trim($get['oneallsso_api_handler']) : '');
        $api_port = (!empty($get['oneallsso_api_port']) ? trim($get['oneallsso_api_port']) : '');
        $use_https = ($api_port == 443 ? true : false);

        // Check if all fields have been filled out.
        if (strlen($api_subdomain) == 0 || strlen($api_subdomain) == 0 || strlen($api_subdomain) == 0)
        {
            $this->send_response($lang['oneallsso_text_ajax_fill_out']);
        }

        // The full domain has been entered.
        if (preg_match("/([a-z0-9\-]+)\.api\.oneall\.com/i", $api_subdomain, $matches))
        {
            $api_subdomain = $matches[1];
        }

        // Check format of the subdomain.
        if (!preg_match("/^[a-z0-9\-]+$/i", $api_subdomain))
        {
            $this->send_response($lang['oneallsso_text_ajax_wrong_subdomain']);
        }

        // Try to establish a connection.
        $client = $this->get_builder()->build($api_handler, $api_subdomain, $api_public_key, $api_private_key, $use_https, 'api.oneall.com');

        // ensure function is working through a test
        $status = false;
        $result = null;

        try
        {
            $result = $client->get('/site.json');
        }
        catch (\Exception $exception)
        {
            $this->send_response($lang['oneallsso_text_ajax_autodetect_error']);
        }

        if (!$result instanceof \Oneall\Phpsdk\Client\Response)
        {
            $this->send_response($lang['oneallsso_text_ajax_autodetect_error']);
        }

        switch ($result->getStatusCode())
        {
            // Connection successful.
            case 200 :
                $body = json_decode($result->getBody());
                if (empty($body->response->result->data->site->subscription_plan->features->has_single_signon))
                {
                    $status_message = $lang['oneallsso_text_ajax_upgrade_your_plan'];
                    $status = false;
                    break;
                }
                $status_message = $lang['oneallsso_text_ajax_settings_ok'];
                $status = true;
            break;

            // Authentication Error.
            case 401 :
                $status_message = $lang['oneallsso_text_ajax_wrong_key'];
            break;

            // Authentication Error.
            case 403 :
                $status_message = $lang['oneallsso_text_ajax_sso_disabled'];
            break;

            // Wrong Subdomain.
            case 404 :
                $status_message = $lang['oneallsso_text_ajax_wrong_subdomain'];
            break;

            // Other error.
            default :
                $status_message = $lang['oneallsso_text_ajax_autodetect_error'];

            break;
        }

        $this->send_response($status_message, $status);
    }

    /**
     * Automatically detect the API settings.
     *
     * @param $lang
     */
    public function autodetect_api_connection($lang)
    {
        $status = 'success';

        // Check CURL HTTPS - Port 443.
        if ($this->check_curl(true) === true)
        {
            $status_message = $lang['oneallsso_text_ajax_curl_ok_443'];
            $handler = 'curl';
            $port = '443';
        }
        // Check FSOCKOPEN HTTPS - Port 443.
        elseif ($this->check_fsockopen(true) == true)
        {
            $status_message = $lang['oneallsso_text_ajax_fsockopen_ok_443'];
            $handler = 'fsockopen';
            $port = '443';
        }
        // Check CURL HTTP - Port 80.
        elseif ($this->check_curl(false) === true)
        {
            $status_message = $lang['oneallsso_text_ajax_curl_ok_80'];
            $handler = 'curl';
            $port = '80';
        }
        // Check FSOCKOPEN HTTP - Port 80.
        elseif ($this->check_fsockopen(false) == true)
        {
            $status_message = '|' . $lang['oneallsso_text_ajax_fsockopen_ok_80'];
            $handler = 'fsockopen';
            $port = '80';
        }
        // No working handler found.
        else
        {
            $status = 'error';
            $status_message = $lang['oneallsso_text_ajax_no_handler'];
            $handler = null;
            $port = null;
        }

        $response = [
            'port' => $port,
            'handler' => $handler,
            'status' => $status,
            'status_message' => $status_message
        ];

        // Output for AJAX.
        die(json_encode($response));
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

    // Checks if CURL can be used.
    private function check_curl($secure = true)
    {
        return $this->check_connectivity('curl', $secure, 'curl', 'curl_exec');
    }

    // Checks if fsockopen can be used.
    private function check_fsockopen($secure = true)
    {
        return $this->check_connectivity('fsockopen', $secure, null, 'fsockopen');
    }

    // Checks if a given handler can be used.
    private function check_connectivity($handler, $isSecure, $extension, $function)
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

        $client = $this->get_builder()->build($handler, 'www', '', '', $isSecure, 'oneall.com');

        // ensure function is working through a test
        $result = $client->get('/ping.html');
        if (!$result || $result->getBody() != 'OK' || $result->getStatusCode() != 200)
        {
            return false;
        }

        return true;
    }

    // Unsets the events used by the module.
    public function unset_events()
    {
        foreach ($this->get_events() as $code => $event)
        {
            $this->model_setting_event->deleteEvent($code);
        }
    }

    // Sets the events used by the module.
    public function set_events()
    {
        foreach ($this->get_events() as $code => $event)
        {
            if (!$this->model_setting_event->getEventByCode($code))
            {
                $this->model_setting_event->addEvent($code, $event['trigger'], $event['action']);
            }
        }
    }

    // Returns the events used by the module.
    private function get_events()
    {
        $events = array(

            // Update Profile.
            'oneallsso_before_update' => [
                'trigger' => 'catalog/controller/account/edit/before',
                'action' => 'extension/module/oneallssoupdate/preUpdate'
            ],
            'oneallsso_after_update' => [
                'trigger' => 'catalog/controller/account/account/before',
                'action' => 'extension/module/oneallssoupdate/postUpdate'
            ],

            // Update password.
            'oneallsso_before_password' => [
                'trigger' => 'catalog/controller/account/password/before',
                'action' => 'extension/module/oneallssoupdate/prePasswordUpdate'
            ],
            'oneallsso_after_password' => [
                'trigger' => 'catalog/controller/account/account/after',
                'action' => 'extension/module/oneallssoupdate/postPasswordUpdate'
            ],

            // Register.
            'oneallsso_before_register' => [
                'trigger' => 'catalog/controller/account/register/before',
                'action' => 'extension/module/oneallssoregister/preRegister'
            ],
            'oneallsso_after_register' => [
                'trigger' => 'catalog/controller/account/success/before',
                'action' => 'extension/module/oneallssoregister/postRegister'
            ],

            // Logout.
            'oneallsso_before_logout' => [
                'trigger' => 'catalog/controller/account/logout/before',
                'action' => 'extension/module/oneallssologin/preLogout'
            ],

            // Login.
            'oneallsso_before_login' => [
                'trigger' => 'catalog/controller/account/login/before',
                'action' => 'extension/module/oneallssologin/preLogin'
            ],
            'oneallsso_after_login' => [
                'trigger' => 'catalog/controller/account/account/before',
                'action' => 'extension/module/oneallssologin/postLogin'
            ],

            // Listener.
            'oneallsso_connect' => [
                'trigger' => 'catalog/controller/common/header/before',
                'action' => 'extension/module/oneallssocallback/callback'
            ]
        );

        return $events;
    }

    /**
     *
     * @return \Oneall\Phpsdk\Client\Builder
     */
    private function get_builder()
    {
        if (!$this->clientBuilder)
        {
            $this->clientBuilder = new \Oneall\Phpsdk\Client\Builder();
        }

        return $this->clientBuilder;
    }

    /**
     * Build json response for ajax communcation
     *
     * @param string $message
     * @param bool $status
     *
     * @return string
     */
    private function send_response($message, $status = false)
    {
        $response = [
            'status' => $status ? 'success' : 'error',
            'status_message' => $message
        ];

        die(json_encode($response));
    }

}