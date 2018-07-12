<?php

/**
 * @package      OneAll OpenCart Database
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

namespace Oneall\Database;

use Cart\Customer;

/**
 * Class sso
 *
 * @package Oneall\Database
 */
class Sso
{
    /**
     * @var \DB
     */
    protected $db;

    /**
     * @var \Config
     */
    protected $config;

    /**
     * @var array
     */
    private $customerGroupDisplay;
    /**
     * @var int
     */
    private $customerGroupId;
    /**
     * @var int
     */
    private $storeId;
    /**
     * @var int
     */
    private $languageId;

    /**
     * @var \ModelAccountCustomer
     */
    private $customerAccount;

    /**
     * Sso constructor.
     *
     * @param \DB                   $db
     * @param \ModelAccountCustomer $customerAccount
     * @param \Config               $config
     */
    public function __construct(\DB $db, \ModelAccountCustomer $customerAccount, \Config $config)
    {
        $this->db                   = $db;
        $this->config               = $config;
        $this->customerGroupDisplay = (array) $this->config->get('customer_group_display');
        $this->customerGroupId      = (int) $this->config->get('customer_group_id');
        $this->storeId              = (int) $this->config->get('store_id');
        $this->languageId           = (int) $this->config->get('language_id');
        $this->customerAccount      = $customerAccount;
    }

    /**
     * Returns opencart customer id associated to OneAll user_token
     *
     * @param string $userToken
     *
     * @return \Cart\Customer|null user or null on error or not found
     */
    public function getUserIdFromToken($userToken)
    {
        $query = ' SELECT * ' .
                 ' FROM ' . DB_PREFIX . 'oasl_user ' .
                 ' WHERE `user_token` = "' . $this->db->escape($userToken) . '"';

        $results = $this->db->query($query);
        if (!$results->num_rows || empty((int) $results->row['customer_id']))
        {
            return null;
        }

        return $results->row;
    }

    /**
     * Returns opencart customer associated to OneAll user_token
     *
     * @param string $userToken
     *
     * @return array|null user or null on error or not found
     */
    public function getUserFromToken($userToken)
    {
        $query = ' SELECT c.* ' .
                 ' FROM ' . DB_PREFIX . 'customer c ' .
                 ' LEFT JOIN ' . DB_PREFIX . 'oasl_user u on u.customer_id = c.customer_id' .
                 ' WHERE `user_token` = "' . $this->db->escape($userToken) . '"';

        $results = $this->db->query($query);
        if (!$results->num_rows || empty((int) $results->row['customer_id']))
        {
            return null;
        }

        return $results->row;
    }

    /**
     * Returns OneAll user_token associated to opencart customer id
     *
     * @param int $id
     *
     * @return string|null null on error or not found
     */
    public function getUserTokenFromId($id)
    {
        $query = ' SELECT * ' .
                 ' FROM ' . DB_PREFIX . 'oasl_user ' .
                 ' WHERE `customer_id` = "' . $this->db->escape($id) . '"';

        $results = $this->db->query($query);
        if (!$results->num_rows || empty($results->row['user_token']))
        {
            return null;
        }

        return $results->row['user_token'];
    }

    /**
     * Returns OneAll identity_token associated to OpenCart customer id
     *
     * @param int    $customerId
     * @param string $provider
     *
     * @return string|null null on error or not found
     */
    public function getIdentityToken($customerId)
    {
        if (empty($customerId))
        {
            return null;
        }

        $customerId = $this->db->escape($customerId);

        $query = 'SELECT ' .
                 '  i.identity_token, ' .
                 '  i.oasl_user_id, ' .
                 '  u.customer_id ' .
                 'FROM `' . DB_PREFIX . 'oasl_user` u ' .
                 '  LEFT JOIN `' . DB_PREFIX . 'oasl_identity` i ON u.oasl_user_id = i.oasl_user_id ' .
                 'WHERE u.customer_id = ' . $customerId . ' ';

        $results = $this->db->query($query);
        if (!$results->num_rows || empty($results->row['identity_token']))
        {
            return null;
        }

        return $results->row['identity_token'];
    }

    /**
     * Link an Opencart customer to OneAll token
     *
     * @param int    $userId
     * @param string $userToken
     *
     * @return null
     */
    public function associateToken($userId, $userToken)
    {
        $id    = $this->db->escape($userId);
        $token = $this->db->escape($userToken);

        $query = ' INSERT INTO ' . DB_PREFIX . 'oasl_user (customer_id, user_token, date_added) ' .
                 '    SELECT *  FROM (SELECT ' . $id . ', "' . $token . '", CURRENT_TIMESTAMP) AS tmp ' .
                 '    WHERE NOT EXISTS( ' .
                 '       SELECT `customer_id`, `user_token`, `date_added` ' .
                 '       FROM ' . DB_PREFIX . 'oasl_user  WHERE customer_id = ' . $id . ' ' .
                 '    ) ' .
                 '    LIMIT 1;';

        $results = $this->db->query($query);

        return (boolean) $results;
    }

    /**
     * @param int    $oneallUserId
     * @param string $identityToken
     * @param string $identityProvider
     *
     * @return null|int oasl_identity id, null on error
     */
    public function saveIdentity($oneallUserId, $identityToken, $identityProvider = 'storage')
    {
        $selectQuery = 'SELECT * FROM `' . DB_PREFIX . 'oasl_identity` ' .
                       ' WHERE identity_token = "' . $this->db->escape($identityToken) . '"';

        // we first check if current customer already have registered the identity token
        $result = $this->db->query($selectQuery);
        if ($result->num_rows)
        {
            return $result->row['oasl_identity_id'];
        }

        $query = 'INSERT INTO `' . DB_PREFIX . 'oasl_identity` ' .
                 ' ( oasl_user_id,identity_token, identity_provider, num_logins, date_added, date_updated) ' .
                 ' VALUES( %s,"%s", "%s", 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);';

        $inserted = $this->db->query(sprintf($query, (int) $oneallUserId, $identityToken, $identityProvider));

        if (!$inserted)
        {
            return null;
        }

        return $this->db->getLastId();
    }

    /**
     * Save a oneallUser (matching between opencart user & oneall user token)
     *
     * @param int    $customerId
     * @param string $userToken
     *
     * @return int|null oasl_user id, null on error
     */
    public function saveOneallUser($customerId, $userToken)
    {
        if (!$customerId || !$userToken)
        {
            return null;
        }

        // we first check if current customer already have registered the identity token
        $query  = 'SELECT * FROM `' . DB_PREFIX . 'oasl_user`  WHERE customer_id = "' . $customerId . '"';
        $result = $this->db->query($query);
        if ($result->num_rows)
        {
            return $result->row['oasl_user_id'];
        }
        unset($result);

        $query = 'INSERT INTO `' . DB_PREFIX . 'oasl_user` (customer_id,user_token, date_added) ' .
                 ' VALUES ( %s, "%s", CURRENT_TIMESTAMP);';

        $inserted = $this->db->query(sprintf($query, $customerId, $userToken));

        if (!$inserted || !$this->db->getLastId())
        {
            return null;
        }

        return $this->db->getLastId();
    }

    /**
     * Check if given credentials are valid. The query has been retrieved from Customer::login() method.
     *
     * @see Customer::login()
     *
     * @param $email
     * @param $password
     *
     * @return Customer
     */
    public function getCustomerFromCredentials($email, $password)
    {
        $md5     = $this->db->escape(md5($password));
        $escaped = $this->db->escape($password);

        $query = ' SELECT * ' .
                 ' FROM ' . DB_PREFIX . 'customer' .
                 ' WHERE ' .
                 '      LOWER(email) = "' . $this->db->escape(utf8_strtolower($email)) . '"
                                            AND (
                                                password = SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1("' . $escaped . '")))))
                                                OR password = "' . $md5 . '" ' .
                 '      ) ' .
                 '      AND status = "1" '
        ;

        $customer_query = $this->db->query($query);
        if (!$customer_query->num_rows)
        {
            return null;
        }

        return $customer_query->row;
    }

    /**
     * Check if given credentials are valid. The query has been retrieved from Customer::login() method.
     *
     * @see Customer::login()
     *
     * @param $email
     *
     * @return null|array
     */
    public function getCustomerFromEmail($email)
    {
        $email = $this->db->escape($email);

        $query = ' SELECT * ' .
                 ' FROM ' . DB_PREFIX . 'customer ' .
                 ' WHERE LOWER(email) = "' . $this->db->escape(utf8_strtolower($email)) . '"';

        $customer_query = $this->db->query($query);
        if (!$customer_query->num_rows)
        {
            return null;
        }

        return $customer_query->row;
    }

    /**
     * @param int $customerId
     *
     * @return array|null null on error or not found
     */
    public function getOaslUser($customerId)
    {
        $query = 'SELECT * ' .
                 '  FROM ' . DB_PREFIX . 'oasl_user u ' .
                 '  LEFT JOIN '.DB_PREFIX.'oasl_identity i ON i.oasl_user_id = u.oasl_user_id ' .
                 '  WHERE u.customer_id =' . $customerId;

        $customer_query = $this->db->query($query);
        if (!$customer_query->num_rows)
        {
            return null;
        }

        return $customer_query->row;
    }

    /**
     * Returns our country id based on the 3 character country iso code.
     *
     * @param string $countryCode 3 char iso_code. Ex. USA
     *
     * @return int|null null if error or not fpund
     */
    public function getCountryId($countryCode)
    {
        if (empty($countryCode))
        {
            return null;
        }

        $query = 'SELECT * FROM ' . DB_PREFIX . 'country  ' .
                 '  WHERE iso_code_3 ="' . $this->db->escape($countryCode) . '"'
                 . ' OR iso_code_2 ="' . $this->db->escape($countryCode) . '"';

        $customer_query = $this->db->query($query);
        if (!$customer_query->num_rows)
        {
            return null;
        }

        return (int) $customer_query->row['country_id'];
    }

    /**
     * Returns our country id based on the 3 character country iso code.
     *
     * @param string $countryCode 3 char iso_code. Ex. USA
     *
     * @return int|null null if error or not fpund
     */
    public function getRegionId($regionName, $countryId)
    {
        if (empty($countryId) || empty($regionName))
        {
            return null;
        }
        $countryId  = $this->db->escape($countryId);
        $regionName = $this->db->escape($regionName);

        $query = 'SELECT zone_id FROM ' . DB_PREFIX . 'zone  ' .
                 '  WHERE `country_id` =' . $countryId . ' AND  `name` ="' . $regionName . '"';

        $customer_query = $this->db->query($query);
        if (!$customer_query->num_rows)
        {
            return null;
        }

        return (int) $customer_query->row['zone_id'];
    }

    /**
     * instert a new address in database
     *
     * @param int   $customerId
     * @param array $data
     *
     * @return int
     */
    public function saveAddress($customerId, array $data)
    {
        $updateMode = false;
        $operation  = 'INSERT INTO';

        $where = [];

        if (!empty($data['address_id']))
        {
            $updateMode = true;
            $operation  = 'UPDATE';
            $where[]    = 'address_id = ' . $data['address_id'];
        }

        $query = $operation . " " . DB_PREFIX . "address SET " .
                 " customer_id = '" . (int) $customerId . "', " .
                 " firstname = '" . $this->db->escape($data['firstname']) . "'," .
                 " lastname = '" . $this->db->escape($data['lastname']) . "', " .
                 " company = '" . $this->db->escape($data['company']) . "', " .
                 " address_1 = '" . $this->db->escape($data['address_1']) . "', " .
                 " address_2 = '" . $this->db->escape($data['address_2']) . "', " .
                 " city = '" . $this->db->escape($data['city']) . "', " .
                 " postcode = '" . $this->db->escape($data['postcode']) . "', " .
                 " country_id = '" . (int) $data['country_id'] . "', " .
                 " zone_id = '" . (int) $data['zone_id'] . "', " .
                 " custom_field = '" . $this->db->escape(isset($data['custom_field']['address']) ? json_encode($data['custom_field']['address']) : '') . "'";

        if ($where)
        {
            $query .= ' WHERE ' . implode(" AND ", $where);
        }

        $this->db->query($query);

        // on insert mode, we return the last inserted id
        if (!$updateMode)
        {
            return $this->db->getLastId();
        }

        return $addressId = $data['address_id'];
    }

    /**
     * Returns customer addresses.
     *
     * @param int $customerId
     *
     * @return null|array null if not found
     */
    public function getAddresses($customerId)
    {
        if (empty($customerId))
        {
            return [];
        }

        $query = 'SELECT a.*, c.iso_code_3 AS code, c.name AS country , z.name AS zone FROM ' . DB_PREFIX . 'address a ' .
                 ' LEFT JOIN ' . DB_PREFIX . 'country c ON c.country_id = a.country_id' .
                 ' LEFT JOIN ' . DB_PREFIX . 'zone z ON z.country_id = a.country_id AND z.zone_id = a.zone_id' .
                 ' WHERE customer_id =' . $this->db->escape($customerId);

        $result = $this->db->query($query);
        if (!$result->num_rows)
        {
            return [];
        }

        return $result->rows;
    }

    /**
     * @param array  $data
     * @param string $remoteAddress
     *
     * @return int saved customer id
     */
    public function saveCustomer($data, $remoteAddress)
    {
        $data = $this->buildCustomerDataArray($data);

        if (empty($data['customer_id']))
        {
            return $this->customerAccount->addCustomer($data);
        }

        $this->customerAccount->editCustomer($data['customer_id'], $data);

        return $data['customer_id'];
    }

    /**
     * Set given address id as the default one for given customer
     *
     * @param int $customerId
     * @param int $addressId
     */
    public function makeDefaultAddress($customerId, $addressId)
    {
        $query = "UPDATE " . DB_PREFIX . "customer " .
                 " SET address_id = " . (int) $addressId .
                 " WHERE customer_id = " . (int) $customerId;

        $this->db->query($query);
    }

    /**
     * @param array $criteria
     *
     * @return array|null null if not found
     */
    public function getCustomer(array $criteria)
    {
        $where = [];
        foreach ($criteria as $key => $value)
        {
            $where[] = 'c.`' . $key . '` ="' . $this->db->escape($value) . '"';
        }

        if (empty($where))
        {
            return null;
        }

        $query = ' SELECT u.oasl_user_id, u.user_token, c.* FROM ' . DB_PREFIX . 'customer c' .
                 ' LEFT JOIN ' . DB_PREFIX . 'oasl_user u ON c.customer_id = u.customer_id ' .
                 ' WHERE ' . implode(' AND ', $where);

        $customer_query = $this->db->query($query);
        if (!$customer_query->num_rows)
        {
            return null;
        }

        return $customer_query->row;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function buildCustomerDataArray(array $data)
    {
        $defaultData = [
            'telephone' => null,
            'fax' => null,
            'password' => null,
            'company' => null,
            'address_1' => null,
            'address_2' => null,
            'city' => null,
            'postcode' => null,
            'country_id' => null,
            'zone_id' => null,
        ];

        // make default values for
        $allData = array_merge($defaultData, $data);

        return $allData;
    }
}
