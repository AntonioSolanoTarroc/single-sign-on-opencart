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
 * Class SessionStorage
 *
 * @package Oneall
 */
class SessionStorage
{
    /**
     * Session var name for our sso session token
     */
    const SSO_TOKEN_SESSION = 'sso_token_session';
    const SSO_CONNECTION    = 'sso_connection';
    /**
     * Session var name for our sso session token
     */
    const LAST_ACTION     = 'user_last_action';
    const ACTION_LOGIN    = 'login';
    const ACTION_PASSWORD = 'password';
    const ACTION_ACCOUNT  = 'account';

    /**
     *
     * @var \Session
     */
    private $session;

    /**
     * SessionStorage constructor.
     *
     * @param \Session $session
     */
    public function __construct(\Session $session)
    {
        $this->session = $session;
    }

    /**
     * Store sso session token in $_SESSION
     *
     * @param string $token
     */
    public function storeSessionToken($token)
    {
        $this->write(self::SSO_TOKEN_SESSION, $token);
    }

    /**
     * Reset sso session token in $_SESSION
     */
    public function resetSessionToken()
    {
        $this->write(self::SSO_TOKEN_SESSION, null);
    }

    /**
     * Reset sso session token in $_SESSION
     */
    public function getSessionToken()
    {
        return $this->read(self::SSO_TOKEN_SESSION);
    }

    /**
     * Store password in sesson
     */
    public function writePassword($password)
    {
        $this->write('oneall', base64_encode($password));
    }

    /**
     * Reset password stored in session and delete it !
     *
     * @return string|null
     */
    public function consumePassword()
    {
        $password = null;
        if (!empty ($this->read('oneall')))
        {
            $password = base64_decode($this->read('oneall'));
            $this->write('oneall', null);
        }

        return $password;
    }

    /**
     * @param boolean $action
     */
    public function setLastAction($action)
    {
        $this->write(self::LAST_ACTION, $action);
    }

    /**
     *
     * @return bool
     */
    public function isLastAction($action)
    {
        return $this->read(self::LAST_ACTION) == $action;
    }

    /**
     *
     * @param string $key
     * @param mixed  $data
     */
    private function write($key, $data)
    {
        $this->session->data [$key] = $data;
    }

    /**
     */
    public function kill()
    {
        $this->session->destroy();
    }

    /**
     *
     * @param string $key
     *
     * @return mixed
     */
    private function read($key)
    {
        if (!isset ($this->session->data [$key]))
        {
            return null;
        }

        return $this->session->data [$key];
    }

    /**
     * .
     * Mark in session we do not have to try to connect the user anymore
     */
    public function allowConnection($allow)
    {
        $this->write(self::SSO_CONNECTION, (bool) $allow);
    }

    /**
     * True if wa can connect the use with sso.
     *
     * @return bool
     */
    public function canTryConnection()
    {
        $canConnect = $this->read(self::SSO_CONNECTION);

        return $canConnect !== false;
    }
}
