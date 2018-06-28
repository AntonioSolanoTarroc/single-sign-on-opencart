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
class ControllerModuleOneallssoRegister extends \Oneall\AbstractOneallSsoController
{
    /**
     * Keep track of user password
     *
     * @return null
     */
    public function preRegister()
    {
        if (empty($_POST))
        {
            return null;
        }

        $this->storage->writePassword($_POST['password']);

        return null;
    }

    /**
     * We'll also  create start
     *
     * @return null
     */
    public function postRegister()
    {
        // if a user is logged
        if (!$this->customer instanceof \Cart\Customer || !$this->customer->getId())
        {
            return null;
        }

        //  we recreate user(and link)
        $this->synchronizer->push($this->customer, $this->storage->consumePassword());

        $this->startSession($this->customer->getId());

        // loading identity data to check if we have something to addd or not
        // getting current email list in order to know if we have to add
        $identityToken = $this->ssoDatabase->getIdentityToken($this->customer->getId());
        $response      = $this->api->getIdentity($identityToken);

        $body          = json_decode($response->getBody());
        $identityData  = new \Oneall\Phpsdk\Response\IdentityFacade($body);
        $identity = $this->buildIdentityDataFromCustomer($this->customer, $identityData);

        // updating distant account
        $mode      = \Oneall\Phpsdk\OneallApi::MODE_UPDATE_REPLACE;
        $userToken = $this->ssoDatabase->getUserTokenFromId($this->customer->getId());
        $this->api->updateUser($userToken, null, null, null, $identity, $mode);

        return null;
    }
}
