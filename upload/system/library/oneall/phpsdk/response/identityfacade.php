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

namespace Oneall\Phpsdk\Response;

class IdentityFacade extends AbstractResponseFacade
{

    /**
     * IdentityFacade constructor.
     *
     * @param object $body
     */
    public function __construct($body)
    {
        parent::__construct($body);

        if (!empty ($body->response->result->data->identity))
        {
            $identity   = $body->response->result->data->identity;
            $this->body = $identity;
        }
    }

    /**
     *
     * @return int
     */
    public function getCountryCode()
    {
        return $this->getObjectValue('addresses/0/code');
    }

    /**
     *
     * @return array
     */
    public function getPhoneNumbers()
    {
        $result = $this->getObjectValue('phoneNumbers');
        if (!is_array($result))
        {
            $result = [];
        }

        return $result;
    }

    /**
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->getObjectValue('name/givenName');
    }

    /**
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->getObjectValue('name/familyName');
    }

    /**
     *
     * @return string
     */
    public function getCompany()
    {
        return $this->getObjectValue('organizations/0/name');
    }

    /**
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->getObjectValue('addresses');
    }

    /**
     *
     * @return string
     */
    public function getAddress1()
    {
        return $this->getObjectValue('addresses/0/streetAddress');
    }

    /**
     *
     * @return string
     */
    public function getAddress2()
    {
        return $this->getObjectValue('addresses/0/complement');
    }

    /**
     *
     * @return string
     */
    public function getCity()
    {
        return $this->getObjectValue('addresses/0/locality');
    }

    /**
     *
     * @return string
     */
    public function getPostcode()
    {
        return $this->getObjectValue('addresses/0/postalCode');
    }

    /**
     * Returns email list
     *
     * @return array
     */
    public function getEmails()
    {
        return (array) $this->getObjectValue('emails');
    }

    /**
     *
     * @return string
     */
    public function getFirstEmail()
    {
        return $this->getObjectValue('emails/0/value');
    }

    /**
     *
     * @return array
     */
    public function getAddresses()
    {
        $addresses = (array) $this->getObjectValue('addresses');

        if (!$addresses)
        {
            $addresses = [];
        }

        return $addresses;
    }
}
