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
 * Class ApiFacade
 *
 * @package Oneall
 */
class ApiFacade
{
    /**
     *
     * @var \Oneall\Phpsdk\OneallApi
     */
    protected $api;

    /**
     * Handler to the oneall error log file.
     *
     * @var \Log
     */
    protected $log;

    /**
     * ApiFacade constructor.
     *
     * @param \Oneall\Phpsdk\OneallApi $api
     * @param \Log                     $log
     */
    public function __construct(\Oneall\Phpsdk\OneallApi $api, \Log $log)
    {
        $this->api = $api;
        $this->log = $log;
    }

    /**
     * Create or update a user on oneall servers (depending if usertoken has been set or not).
     *
     * @param array       $identityData
     * @param string|null $userToken
     * @param string|null $email
     * @param string|null $password
     * @param int|null    $externalId
     *
     * @return null|\Stdclass null on error
     */
    public function pushUser(array $identityData, $userToken, $email = null, $password = null, $externalId = null)
    {
        // User already exist
        if ($userToken)
        {
            return $this->updateUser($userToken, $identityData, $externalId, $email, $password);
        }

        return $this->createUser($identityData, $externalId, $email, $password);
    }

    /**
     *
     * @param string $identityToken
     *
     * @return null
     */
    public function getSsoSessionToken($identityToken)
    {
        $sessionResponse = $this->api->readIdentitySession($identityToken);
        $body            = json_decode($sessionResponse->getBody());
        $sessionToken    = null;

        if (isset ($body->response->result->data->sso_session->sso_session_token))
        {
            $sessionToken = $body->response->result->data->sso_session->sso_session_token;
        }

        return $sessionToken;
    }

    /**
     * Return first user email
     *
     * @param
     *            $userToken
     *
     * @return string
     */
    public function getUserEmail($userToken)
    {
        $userResponse = $this->api->getUser($userToken);
        $userDetails  = json_decode($userResponse->getBody());
        $response     = new \Oneall\Phpsdk\Response\IdentityFacade ($userDetails);

        return $response->getFirstEmail();
    }

    /**
     *
     * @param array  $identityData
     * @param int    $externalId
     * @param string $email
     * @param string $password
     *
     * @return mixed|null
     */
    protected function createUser($identityData, $externalId, $email, $password)
    {
        // if the user does not exist, we just create a new one with its credentials.
        $response = $this->api->createUser($identityData, null, $email, $password);

        // logging errors
        if ($response->getStatusCode() > 201)
        {
             $this->log->write(sprintf('Unable to create distant user "%s" : [%s] %s', $email, $response->getStatusCode(), $response->getReasonPhrase()));
            return null;
        }

        return json_decode($response->getBody());
    }

    /**
     *
     * @param string $userToken
     * @param array  $identityData
     * @param null   $externalId
     * @param null   $login
     * @param null   $password
     *
     * @return mixed|null
     */
    protected function updateUser($userToken, array $identityData, $externalId = null, $login = null, $password = null)
    {
        $response = $this->api->updateUser($userToken, $externalId, $login, $password, $identityData);
        return json_decode($response->getBody());
    }

    /**
     * Read SSO Identity Session
     *
     * @param string $identityToken
     * @param array  $options
     *
     * @see http://docs.oneall.com/api/resources/sso/identity/read-session/
     *
     * @return \Oneall\Phpsdk\Client\Response
     */
    public function readIdentitySession($identityToken, array $options = [])
    {
        return $this->getClient()->get('/sso/sessions/identities/' . $identityToken . '.json', $options);
    }

}
