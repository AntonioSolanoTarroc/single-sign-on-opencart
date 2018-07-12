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
class ControllerExtensionModuleOneallSsoCallback extends \Oneall\AbstractOneallSsoController
{
    /**
     * Try to connect a user with the given posted token
     *
     * @return null
     */
    public function callback()
    {
        if ($this->customer instanceof \Cart\Customer && $this->customer->getId())
        {
            if (!$this->storage->getSessionToken() && $this->storage->canTryConnection())
            {
                $this->synchronizer->push($this->customer);
                $this->startSession($this->customer->getId());
            }
        }

        if (empty($_POST['connection_token']))
        {
            if (!$this->storage->getSessionToken() && $this->storage->canTryConnection())
            {
                $this->addSsoLibrary();
            }

            return null;
        }

        $result = $this->api->getConnection($_POST['connection_token']);
        $response = new \Oneall\Phpsdk\Response\ResponseFacade(json_decode($result->getBody()));

        // getting tokens from the received connection token
        $userToken = $response->getUserToken();
        $identityToken = $response->getIdentityToken();
        $sessionToken = $this->facade->getSsoSessionToken($identityToken);

        // pulling profil (& create a customer if required)
        $customerId = $this->synchronizer->pull($identityToken, $userToken);

        if (!$customerId)
        {
            $this->storage->allowConnection(false);

            return null;
        }

        $this->addSsoLibrary($sessionToken);
        $this->login($userToken, $identityToken, $customerId);
        $this->storage->storeSessionToken($sessionToken);

        return null;
    }
}
