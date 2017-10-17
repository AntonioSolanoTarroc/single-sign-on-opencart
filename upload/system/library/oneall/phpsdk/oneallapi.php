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

namespace Oneall\Phpsdk;

use Oneall\Phpsdk\Client\ClientInterface;
use Oneall\Phpsdk\Client\ProxyConfiguration;

class OneallApi
{
    const MODE_UPDATE_REPLACE = 'replace';
    const MODE_UPDATE_APPEND  = 'append';

    /**
     *
     * @var ClientInterface
     */
    protected $client;

    /**
     *
     * @var ProxyConfiguration
     */
    protected $proxy;

    /**
     * Token storage eto communicate between api.
     *
     * All kind of token are stored : identities, user, connection, ...
     *
     * @var array
     */
    protected $token = [];

    /**
     * OneallApi constructor.
     *
     * @param \Oneall\Phpsdk\Client\ClientInterface         $client
     * @param \Oneall\Phpsdk\Client\ProxyConfiguration|null $proxy
     */
    public function __construct(ClientInterface $client, ProxyConfiguration $proxy = null)
    {
        $this->client = $client;
        $this->proxy  = $proxy;
    }

    /**
     *
     * @return \Oneall\Phpsdk\Client\ClientInterface
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     *
     * @param \Oneall\Phpsdk\Client\ClientInterface $client
     *
     * @return $this
     */
    public function setClient(ClientInterface $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     *
     * @return \Oneall\Phpsdk\Client\ProxyConfiguration
     */
    public function getProxy()
    {
        return $this->proxy;
    }

    /**
     *
     * @param \Oneall\Phpsdk\Client\ProxyConfiguration $proxy
     *
     * @return $this
     */
    public function setProxy(ProxyConfiguration $proxy)
    {
        $this->proxy = $proxy;

        return $this;
    }

    /**
     * Read Connection Details
     *
     * @param string $token
     * @param array  $options
     *
     * @see http://docs.oneall.com/api/resources/connections/read-connection-details/
     *
     * @return \Oneall\Phpsdk\Client\Response
     */
    public function getConnection($token, array $options = [])
    {
        $response = $this->getClient()->get('/connections/' . $token . '.json', $options);

        return $response;
    }

    /**
     * Read Identity Details
     *
     * @param string $identityToken
     * @param array  $options
     *
     * @see http://docs.oneall.com/api/resources/identities/list-all-identities/
     *
     * @return \Oneall\Phpsdk\Client\Response
     */
    public function getIdentity($identityToken, array $options = [])
    {
        return $this->getClient()->get('/identities/' . $identityToken . '.json', $options);
    }

    /**
     * Retrieve user details
     *
     * @param string $token
     * @param array  $options
     *
     * @see http://docs.oneall.com/api/resources/users/read-user-details/
     *
     * @return \Oneall\Phpsdk\Client\Response
     */
    public function getUser($token, array $options = [])
    {
        return $this->getClient()->get('/users/' . $token . '.json', $options);
    }

    /**
     * Add user to storage
     *
     * @param string $externalId
     * @param string $login
     * @param string $password
     * @param array  $identity
     * @param array  $options
     *
     * @see http://docs.oneall.com/api/resources/storage/users/create-user/
     *
     * @return \Oneall\Phpsdk\Client\Response
     */
    public function createUser(
        array $identity,
        $externalId = null,
        $login = null,
        $password = null,
        array $options = []
    ) {
        $data = [
            "request" => [
                "user" => [
                    "identity" => $identity
                ]
            ]
        ];

        $data = $this->addInfo($data, 'request/user/externalid', $externalId);
        $data = $this->addInfo($data, 'request/user/login', $login);
        $data = $this->addInfo($data, 'request/user/password', $password);

        return $this->getClient()->post('/storage/users.json', $data, $options);
    }

    /**
     * Update user data
     *
     * @param string $userToken
     * @param mixed  $externalId
     * @param string $login
     * @param string $password
     * @param array  $identity
     * @param string $mode
     * @param array  $options
     *
     * @see http://docs.oneall.com/api/resources/storage/users/update-user/
     *
     * @return null|\Oneall\Phpsdk\Client\Response null if nothing to update
     */
    public function updateUser(
        $userToken,
        $externalId = null,
        $login = null,
        $password = null,
        array $identity = [],
        $mode = self::MODE_UPDATE_REPLACE,
        array $options = []
    ) {
        if (empty ($externalId) && empty ($login) && empty ($password) && empty ($identity))
        {
            return null;
        }

        $data = [
            "request" => [
                'update_mode' => $mode,
                "user" => []
            ]
        ];

        $data = $this->addInfo($data, 'request/user/externalid', $externalId);
        $data = $this->addInfo($data, 'request/user/login', $login);
        $data = $this->addInfo($data, 'request/user/password', $password);
        $data = $this->addInfo($data, 'request/user/identity', $identity);

        return $this->getClient()->put('/storage/users/' . $userToken . '.json', $data, $options);
    }

    /**
     * Look up user by its credentials
     *
     * @param string      $login
     * @param string|null $password
     * @param array       $options
     *
     * @see http://docs.oneall.com/api/resources/storage/users/lookup-user/
     *
     * @return \Oneall\Phpsdk\Client\Response
     */
    public function lookUpByCredentials($login, $password = null, array $options = [])
    {
        $data = [
            "request" => [
                "user" => [
                    "login" => $login
                ]
            ]
        ];

        $data = $this->addInfo($data, 'request/user/password', $password);

        return $this->getClient()->post('/storage/users/user/lookup.json', $data, $options);
    }

    /**
     * Start SSO Identity Session
     *
     * @see http://docs.oneall.com/api/resources/sso/identity/start-session/
     *
     * @param string      $identityToken
     * @param string|null $topRealm
     * @param string|null $subRealm
     * @param string|null $lifetime
     * @param array       $options
     *
     * @return \Oneall\Phpsdk\Client\Response
     */
    public function startIdentitySession(
        $identityToken,
        $topRealm = null,
        $subRealm = null,
        $lifetime = null,
        array $options = []
    ) {
        $data = [
            'request' => [
                'sso_session' => []
            ]
        ];

        $data = $this->addInfo($data, '/request/sso_session/top_realm', $topRealm);
        $data = $this->addInfo($data, '/request/sso_session/sub_realm', $subRealm);
        $data = $this->addInfo($data, '/request/sso_session/lifetime', $lifetime);

        return $this->getClient()->put('/sso/sessions/identities/' . $identityToken . '.json', $data, $options);
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

    /**
     * Read SSO Session
     *
     * @param string $sessionToken
     * @param array  $options
     *
     * @see http://docs.oneall.com/api/resources/sso/read-session-details/
     *
     * @return \Oneall\Phpsdk\Client\Response
     */
    public function deleteSsoSession($sessionToken, array $options = [])
    {
        return $this->getClient()->delete('/sso/sessions/' . $sessionToken . '.json?confirm_deletion=true', $options);
    }

    /**
     * Merge value in array following the given path
     *
     * While our request json structure can be quite complex, this method is used to ease conditional data injection in
     * deep array
     *
     * @param array  $array
     * @param string $path
     *            path in array , separated with /
     * @param mixed  $value
     *
     * @example $api->addInfo($exampleArray, 'long/path/to/the/element/to/set/in/array', $theValueToSet );
     *
     * @return array the modified array
     */
    protected function addInfo(array $array, $path, $value)
    {
        if ($value === null || (is_array($value) && empty ($value)))
        {
            return $array;
        }

        $parts = array_filter(explode('/', $path));

        $pointer = &$array;
        foreach ($parts as $part)
        {
            if (!is_array($pointer) || !array_key_exists($part, $pointer))
            {
                $pointer [$part] = null;
            }

            $pointer = &$pointer [$part];
        }
        $pointer = $value;

        return $array;
    }
}
