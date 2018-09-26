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

namespace Oneall;

use Oneall\Database\Sso;
use Oneall\Phpsdk\Client\Builder;
use Oneall\Phpsdk\OneallApi;
use Oneall\Phpsdk\Response\IdentityFacade;

/**
 * Class AbstractOneallSsoController
 *
 * @package Oneall
 */
class AbstractOneallSsoController extends \Controller
{
    /**
     * Sso settings (those save from admin panel)
     *
     * @var \Oneall\sso_settings
     */
    protected $settings;

    /**
     *
     * @var \Oneall\Phpsdk\OneallApi
     */
    protected $api;

    /**
     *
     * @var \Oneall\ApiFacade
     */
    protected $facade;

    /**
     *
     * @var \Oneall\Database\Sso
     */
    protected $ssoDatabase;

    /**
     *
     * @var \Oneall\DataExporter
     */
    protected $exporter;

    /**
     *
     * @var \Oneall\SessionStorage
     */
    protected $storage;
    /**
     *
     * @var \Oneall\Synchronizer
     */
    protected $synchronizer;

    /**
     * AbstractOneallSsoController constructor.
     *
     * @param
     *            $registry
     */
    public function __construct($registry)
    {
        parent::__construct($registry);

        $this->storage = new SessionStorage ($this->session);

        // Load Language
        $this->load->language('extension/module/oneallsso');

        // Load settings
        $this->load->model('setting/setting');
        $this->settings = new sso_settings ($this->model_setting_setting->getSetting('module_oneallsso'));

        // build client handler
        $client = $this->buildClient($this->settings);
        $this->api = new OneallApi ($client);

        // adding required scripts
        $this->document->addScript($this->settings->get_library_uri());

        if ($this->storage->getSessionToken())
        {
            $this->addSsoLibrary($this->storage->getSessionToken());
        }

        $this->facade = new \Oneall\ApiFacade ($this->api, $this->log);

        $this->load->model('account/customer');

        $accountCustomer = new \ModelAccountCustomer ($this->registry);

        // Delegate sql queries to ssoDatabase objects
        $this->ssoDatabase = new Sso ($this->db, $accountCustomer, $this->config);
        $this->exporter = new DataExporter ($this->ssoDatabase);
        $this->synchronizer = new Synchronizer ($this->ssoDatabase, $this->api, $this->facade, $this->exporter, $this->settings);

        return true;
    }

    /**
     *
     * @param \Oneall\sso_settings $settings
     *
     * @return \Oneall\Phpsdk\Client\AbstractClient
     */
    private function buildClient(sso_settings $settings)
    {
        $builder = new Builder ();
        $client = $builder->build($settings->get_api_handler(), $settings->get_api_subdomain(), $settings->get_api_public_key(), $settings->get_api_private_key(), $settings->get_api_port() == 443, 'api.' . $settings->get_domain());

        return $client;
    }

    /**
     * Create a new sso session on all required server for the given user id.
     *
     * @param int $customerId
     */
    protected function startSession($customerId)
    {
        $identityToken = $this->ssoDatabase->getIdentityToken($customerId);

        // start a new session on oneall servers
        $response = $this->api->startIdentitySession($identityToken);
        // Get & store the sso session token
        $sessionToken = $this->facade->getSsoSessionToken($identityToken);
        $this->storage->storeSessionToken($sessionToken);

        // add js to create sso cookie.
        $this->addSsoLibrary ($sessionToken);
    }

    /**
     *
     * @param string|null $sessionToken
     *
     * @return null
     */
    protected function addSsoLibrary($sessionToken = null)
    {
        $this->removeSsoLibrary();

        // Do we have a token?
        if (strlen(trim($sessionToken)) > 0)
        {
            $suffix = '?sso_session_token=' . $sessionToken;
        }
        else
        {
            $suffix = '';
        }

        // add js to create sso cookie.
        $this->document->addScript('catalog/view/javascript/oneall/sso.js' . $suffix);

        return null;
    }

    /**
     *
     */
    protected function removeSsoLibrary()
    {
        // we'll first remove sso library from document scripts
        $propertyReflection = new \ReflectionProperty ($this->document, 'scripts');
        $propertyReflection->setAccessible(true);
        $scripts = $propertyReflection->getValue($this->document);

        foreach ($scripts ['header'] as $key => $script)
        {
            if (strpos($script, 'oneall/sso_library.js') > 0)
            {
                unset ($scripts ['header'] [$key]);
            }
        }

        $propertyReflection = new \ReflectionProperty ($this->document, 'scripts');
        $propertyReflection->setAccessible(true);
        $propertyReflection->setValue($this->document, $scripts);
    }

    /**
     * @param \Cart\Customer                         $customer
     * @param \Oneall\Phpsdk\Response\IdentityFacade $existingIdentity
     *
     * @return array
     */
    protected function buildIdentityDataFromCustomer(\Cart\Customer $customer, IdentityFacade $existingIdentity)
    {
        $identity = [
            "name" => [
                "givenName" => $customer->getFirstname(),
                "familyName" => $customer->getLastname(),
            ],
        ];

        // adding emails
        $identity ["emails"] = [];
        $identity ["emails"] = $existingIdentity->getEmails();
        if (!$this->emailAlreadyExists($existingIdentity, $this->customer->getEmail()))
        {
            $identity ["emails"][] = [
                "value" => $this->customer->getEmail(),
                'is_verified' => false,
            ];
        }

        // adding numbers
        $numbers = $existingIdentity->getPhoneNumbers();
        $newNumbers = [
            'home' => $this->customer->getTelephone(),
        ];
        $identity ["phoneNumbers"] = $this->updatePhoneNumbers($numbers, $newNumbers);

        return $identity;
    }

    /**
     * @param \Oneall\Phpsdk\Response\IdentityFacade $identityData
     * @param  string                                $newEmail
     *
     * @return bool
     */
    private function emailAlreadyExists(\Oneall\Phpsdk\Response\IdentityFacade $identityData, $newEmail)
    {
        foreach ($identityData->getEmails() as $email)
        {
            if ($email->value == $newEmail)
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Update oneall identity numbers
     *
     * @param array $numbers    Numbers from identity response
     * @param array $newNumbers array of number to add (key=type, value=number)
     *
     * @return mixed
     */
    private function updatePhoneNumbers($numbers, array $newNumbers)
    {
        foreach ($numbers as &$number)
        {
            if (!empty($number->type) && !empty($newNumbers[$number->type]))
            {
                $number->value = $newNumbers[$number->type];
            }
        }

        return $numbers;
    }

    /**
     * Log the user in (if allowed by OC configuration) and create oneall user/identities if required.
     *
     * @param string $userToken
     * @param string $identityToken
     *
     * @return null|\Cart\Customer
     */
    protected function login($userToken, $identityToken, $customerId)
    {
        // we'll check if given user is associated to the customer,
        // if not, we'll check if the config allow to create the link
        // if the config forbid it, we quit : unage to assocaite a customer to the oneall user
        $oaUser = $this->ssoDatabase->getOaslUser($customerId);
        if (!$oaUser)
        {
            // not linked - are we allowed to link
            if (!$this->settings->is_accounts_link_automatic())
            {
                // should redirect to login page with message
                $this->storage->allowConnection(false);

                return null;
            }
        }

        $customer = $this->ssoDatabase->getCustomer(['customer_id' => $customerId]);
        // if no customer has been found to login, we just quit.
        if (!$customer)
        {
            $this->storage->allowConnection(false);

            return null;
        }

        $this->ssoDatabase->associateToken($customerId, $userToken);

        // Here we have a customer associated to the user token.
        // Lets check if the user is allowed to connect (opencart config)
        if ($customer['status'] != 1)
        {
            $this->storage->allowConnection(false);
            throw new \RuntimeException('Please, sign in with your credentials.');
        }

        $this->storage->allowConnection(true);
        // here create the identity if not created.
        if (!$identity = $this->ssoDatabase->getIdentityToken($customerId))
        {
            $oaUser = $this->ssoDatabase->getOaslUser($customerId);
            $this->ssoDatabase->saveIdentity($oaUser['oasl_user_id'], $identityToken);
        }

        $sessionToken = $this->facade->getSsoSessionToken($identityToken);
        $this->storage->storeSessionToken($sessionToken);

        // adding library with the session token (in order to build the oasso cookie)
        $this->addSsoLibrary($this->storage->getSessionToken());

        $this->customer->login($customer['email'], '', true);

        return $this->customer;
    }
}
