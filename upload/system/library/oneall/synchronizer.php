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

use Cart\Customer;
use Oneall\Database\Sso;
use Oneall\Phpsdk\OneallApi;
use Oneall\Phpsdk\Response\IdentityFacade;
use Oneall\Phpsdk\Response\ResponseFacade;

/**
 * Class Synchronizer
 *
 * Use to synchronize data between OC customer & OA user
 *
 * @package Oneall
 */
class Synchronizer
{
    /**
     *
     * @var \Oneall\Database\Sso
     */
    private $database;

    /**
     *
     * @var Api\AbstractApi
     */
    private $api;

    /**
     *
     * @var \Oneall\DataExporter
     */
    protected $exporter;

    /**
     *
     * @var \Oneall\sso_settings
     */
    protected $config;

    /**
     * Synchronizer constructor.
     *
     * @param Sso                      $database
     * @param \Oneall\Phpsdk\OneallApi $api
     */
    public function __construct(
        Sso $database,
        OneallApi $api,
        ApiFacade $apiFacade,
        DataExporter $exporter,
        sso_settings $settings
    ) {
        $this->database = $database;
        $this->api = $api;
        $this->apiFacade = $apiFacade;
        $this->exporter = $exporter;
        $this->settings = $settings;
    }

    /**
     * We pull whole user profile associated to the userToken and identityToken.
     *
     * Also create the OpenCart customer account if required.
     *
     * @param string $identityToken
     * @param string $userToken
     *
     * @return int return saved customer id
     */
    public function pull($identityToken, $userToken)
    {
        // first thing to do is to find the customer to update (or create)
        $userEmail = $this->apiFacade->getUserEmail($userToken);
        $customer = $this->identifyCustomer($userToken, $userEmail);
        if (!$customer)
        {
            // account creation is not allowed
            if (!$this->settings->is_accounts_create_auto())
            {
                return null;
            }

            $customer = [];
        }

        $rawResponse = $this->api->getIdentity($identityToken);
        $identityResponse = new IdentityFacade (json_decode($rawResponse->getBody()));

        $customer = $this->mergeCustomerData($customer, $identityResponse);
        $customerId = $this->database->saveCustomer($customer, $_SERVER ['REMOTE_ADDR']);

        $this->pullAddresses((array) $identityResponse->getAddresses(), $customerId);

        return $customerId;
    }

    /**
     *
     * @param \Cart\Customer $customer
     * @param null           $password
     *
     * @return null
     */
    public function push(Customer $customer, $password = null)
    {
        $data = $this->exporter->exportCustomer($customer);
        $userToken = $this->database->getIdentityToken($customer->getId());

        // if no token in DB, we have a new customer, freshly registered.
        // we'll check if an account for its email has been created.
        if (!$userToken)
        {
            $response = $this->api->lookUpByCredentials($customer->getEmail());
            $userResponse = new ResponseFacade (json_decode($response->getBody()));

            $userToken = $userResponse->getUserToken();

            unset ($response);
        }

        $response = $this->apiFacade->pushUser($data, $userToken, $customer->getEmail(), $password, $customer->getId());
        if (!$response)
        {
            return null;
        }

        $responseFacade = new \Oneall\Phpsdk\Response\ResponseFacade ($response);

        // link customer to the user/identity token if the customer is already created.
        // (
        $userToken = $responseFacade->getUserToken();
        $identityToken = $responseFacade->getIdentityToken();
        $identityProvider = $responseFacade->getProvider();

        $oneAllUserId = $this->database->saveOneallUser($customer->getId(), $userToken);
        $this->database->saveIdentity($oneAllUserId, $identityToken, $identityProvider);
    }

    /**
     *
     * @param array    $profileAddresses
     * @param int|null $customerId
     *
     * @return array
     */
    protected function pullAddresses(array $profileAddresses, $customerId)
    {
        $customerAddresses = $this->database->getAddresses($customerId);
        $addresses = [];

        // escape case 1 : if we have more than 1 address in opencart, we do not know which one to update
        $escapeCase1 = (count($customerAddresses) > 1);
        // case 2 if we have one address, there must be only one address in the profile otherwise, we do not know
        // which one to use for updatation
        $escapeCase2 = (count($customerAddresses) == 1 && count($profileAddresses) != 1);
        // when we do not hane any address in opencart, we take all !

        if ($escapeCase1 || $escapeCase2)
        {
            // no address to update
            return [];
        }

        // we will import all profile address
        // importation case 0 <- *
        // importation case 1 <- 1
        while (!empty ($profileAddresses))
        {
            $customerAddress = array_pop($customerAddresses);
            // if there is no more customer addess, we create an empty one
            if (!$customerAddress)
            {
                $customerAddress = [];
            }

            $profileAddress = (array) array_pop($profileAddresses);

            $address = $this->mergeDataIntoOpencartAddress($customerAddress, $profileAddress);
            $addressId = $this->database->saveAddress($customerId, $address);
            $this->database->makeDefaultAddress($customerId, $addressId);
        }

        // other case cannot be handled
        return $addresses;
    }

    /**
     *
     * @param
     *            $userToken
     * @param
     *            $userEmail
     *
     * @return array|null
     */
    protected function identifyCustomer($userToken, $userEmail)
    {
        $customer = $this->database->getUserFromToken($userToken);
        if ($customer)
        {
            return $customer;
        }

        return $this->database->getCustomerFromEmail($userEmail);
    }

    /**
     *
     * @param array          $customer
     * @param IdentityFacade $identity
     *
     * @return array
     */
    protected function mergeCustomerData(array $customer, IdentityFacade $identity)
    {
        $customer ['firstname'] = $identity->getFirstname();
        $customer ['lastname'] = $identity->getLastname();
        $customer ['email'] = $identity->getFirstEmail();

        $numbers = $identity->getPhoneNumbers();
        $unknown = null;
        foreach ($numbers as $number)
        {
            switch ($number->type)
            {
                case 'fax' :
                    $this->fax = $number->value;
                    break;

                case 'home' :
                    $this->telephone = $number->value;
                    break;
                default :
                    if ($unknown === null)
                    {
                        $unknown = $number->value;
                    }
            }
        }

        return $customer;
    }

    /**
     *
     * @param array $opencartAddress
     * @param array $profileAddress
     *
     * @return array
     */
    protected function mergeDataIntoOpencartAddress(array $opencartAddress, array $profileAddress)
    {
        $structure = [
            'firstname' => '',
            'lastname' => '',
            'company' => '',
            'address_1' => '',
            'address_2' => '',
            'city' => '',
            'postcode' => '',
            'country_id' => '',
            'zone_id' => '',
            'custom_field' => ''
        ];

        // first ensure all
        $opencartAddress = array_merge($structure, $opencartAddress);
        if (!empty ($profileAddress['code']))
        {
            $countryId = $this->database->getCountryId($profileAddress['code']);
            $opencartAddress['country_id'] = $countryId;

            if (!empty ($profileAddress['region']) && $countryId)
            {
                $regionId = $this->database->getRegionId($profileAddress['region'], $countryId);
                $opencartAddress['zone_id'] = $regionId;
            }
        }

        $opencartAddress['firstname'] = !empty($profileAddress['firstName']) ? $profileAddress['firstName'] : '';
        $opencartAddress['lastname'] = !empty($profileAddress['lastName']) ? $profileAddress['lastName'] : '';
        $opencartAddress['company'] = !empty($profileAddress['companyName']) ? $profileAddress['companyName'] : '';
        $opencartAddress['address_1'] = !empty($profileAddress['streetAddress']) ? $profileAddress['streetAddress'] : '';
        $opencartAddress['address_2'] = !empty($profileAddress['complement']) ? $profileAddress['complement'] : '';
        $opencartAddress['city'] = !empty($profileAddress['locality']) ? $profileAddress['locality'] : '';
        $opencartAddress['postcode'] = !empty($profileAddress['postalCode']) ? $profileAddress['postalCode'] : '';

        return $opencartAddress;
    }
}
