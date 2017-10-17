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

/**
 * Class DataExporter
 *
 * @package Oneall
 */
class DataExporter
{
    /**
     *
     * @var \Oneall\Database\Sso
     */
    private $database;

    /**
     * DataExporter constructor.
     *
     * @param \Oneall\Database\Sso $database
     */
    public function __construct(\Oneall\Database\Sso $database)
    {
        $this->database = $database;
    }

    /**
     *
     * @param \Cart\Customer $customer
     *
     * @return array
     *
     */
    public function exportCustomer(\Cart\Customer $customer)
    {
        $addresses = $this->database->getAddresses($customer->getId());

        $exportedAddresses = [];
        foreach ($addresses as $address)
        {
            $exportedAddresses [] = $this->exportAddress($address, $customer);
        }

        $data = [
            "name" => [
                "givenName" => $customer->getFirstName(),
                "familyName" => $customer->getLastName()
            ],
            "addresses" => $exportedAddresses
        ];

        if ($customer->getEmail())
        {
            $data ['emails'] [] = [
                "value" => $customer->getEmail(),
                "is_verified" => false
            ];
        }

        if ($customer->getTelephone())
        {
            $data ['phoneNumbers'] [] = [
                "value" => $customer->getTelephone(),
                "type" => "home"
            ];
        }

        if ($customer->getFax())
        {
            $data ['phoneNumbers'] [] = [
                "value" => $customer->getFax(),
                "type" => "fax"
            ];
        }

        return $this->cleanUpExport($data);
    }

    /**
     * Export an address in identity/address format (oneall api)
     *
     * @param array          $address
     * @param \Cart\Customer $customer
     *
     * @return array
     */
    public function exportAddress(array $address, \Cart\Customer $customer)
    {
        $export = [
            'type' => 'shipping',
            'companyName' => $address ['company'],
            'firstName' => $address ['firstname'],
            'lastName' => $address ['lastname'],
            'phoneNumber' => $customer->getTelephone(),
            'faxNumber' => $customer->getFax(),
            'streetAddress' => $address ['address_1'], // address 1
            'complement' => $address ['address_2'], // address 2
            'locality' => $address ['city'],
            'region' => $address ['zone'],
            'postalCode' => $address ['postcode'],
            'country' => $address ['country']
        ];

        return $this->cleanUpExport($export);
    }

    /**
     * Remove recursively all null value or empty array from the given export array
     *
     * @param array $export
     *
     * @return array
     */
    private function cleanUpExport(array $export)
    {
        foreach ($export as $key => &$value)
        {
            if (is_array($value))
            {
                $value = $this->cleanUpExport($value);
            }

            if (is_null($value) || (is_array($value) && empty ($value)))
            {
                unset ($export [$key]);
            }
        }

        return $export;
    }
}
