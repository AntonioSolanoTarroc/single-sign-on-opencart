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
 * Class sso_settings
 *
 * Used to handle out specific sso settings
 *
 * @package Oneall
 */
class sso_settings
{
    /**
     *
     * @var string
     */
    protected $api_subdomain;
    /**
     *
     * @var string
     */
    protected $session_subrealm;

    /**
     *
     * @var string
     */
    protected $session_realm;

    /**
     *
     * @var string
     */
    protected $private_key;

    /**
     *
     * @var string
     */
    protected $public_key;

    /**
     *
     * @var int
     */
    protected $session_lifetime;

    /**
     *
     * @var int
     */
    protected $accounts_link_unverified;

    /**
     *
     * @var int
     */
    protected $accounts_link_automatic;

    /**
     *
     * @var int
     */
    protected $accounts_create_sendmail;

    /**
     *
     * @var int
     */
    protected $accounts_create_auto;

    /**
     *
     * @var int
     */
    protected $port;

    /**
     *
     * @var string
     */
    protected $handler;

    /**
     * sso_settings constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        foreach ($config as $property => $value)
        {
            $prefix = 'oasso_';
            if (strpos($property, $prefix) === 0)
            {
                $property = substr($property, strlen($prefix));
            }

            if (property_exists($this, $property))
            {
                $this->$property = $value;
            }
        }
    }

    /**
     *
     * @return string
     */
    public function get_api_subdomain()
    {
        return $this->api_subdomain;
    }

    /**
     *
     * @param string $api_subdomain
     *
     * @return $this
     */
    public function set_api_subdomain($api_subdomain)
    {
        $this->api_subdomain = $api_subdomain;

        return $this;
    }

    /**
     *
     * @return string
     */
    public function get_session_subrealm()
    {
        return $this->session_subrealm;
    }

    /**
     *
     * @param string $session_subrealm
     *
     * @return $this
     */
    public function set_session_subrealm($session_subrealm)
    {
        $this->session_subrealm = $session_subrealm;

        return $this;
    }

    /**
     *
     * @return string
     */
    public function get_session_realm()
    {
        return $this->session_realm;
    }

    /**
     *
     * @param string $session_realm
     *
     * @return $this
     */
    public function set_session_realm($session_realm)
    {
        $this->session_realm = $session_realm;

        return $this;
    }

    /**
     *
     * @return string
     */
    public function get_private_key()
    {
        return $this->private_key;
    }

    /**
     *
     * @param string $private_key
     *
     * @return $this
     */
    public function set_private_key($private_key)
    {
        $this->private_key = $private_key;

        return $this;
    }

    /**
     *
     * @return string
     */
    public function get_public_key()
    {
        return $this->public_key;
    }

    /**
     *
     * @param string $public_key
     *
     * @return $this
     */
    public function set_public_key($public_key)
    {
        $this->public_key = $public_key;

        return $this;
    }

    /**
     *
     * @return int
     */
    public function get_session_lifetime()
    {
        return $this->session_lifetime;
    }

    /**
     *
     * @param int $session_lifetime
     *
     * @return $this
     */
    public function set_session_lifetime($session_lifetime)
    {
        $this->session_lifetime = $session_lifetime;

        return $this;
    }

    /**
     *
     * @return int
     */
    public function is_accounts_link_unverified()
    {
        return $this->accounts_link_unverified;
    }

    /**
     *
     * @param int $accounts_link_unverified
     *
     * @return $this
     */
    public function set_accounts_link_unverified($accounts_link_unverified)
    {
        $this->accounts_link_unverified = $accounts_link_unverified;

        return $this;
    }

    /**
     *
     * @return int
     */
    public function is_accounts_link_automatic()
    {
        return $this->accounts_link_automatic;
    }

    /**
     *
     * @param int $accounts_link_automatic
     *
     * @return $this
     */
    public function set_accounts_link_automatic($accounts_link_automatic)
    {
        $this->accounts_link_automatic = $accounts_link_automatic;

        return $this;
    }

    /**
     *
     * @return int
     */
    public function is_accounts_create_sendmail()
    {
        return $this->accounts_create_sendmail;
    }

    /**
     *
     * @param int $accounts_create_sendmail
     *
     * @return $this
     */
    public function set_accounts_create_sendmail($accounts_create_sendmail)
    {
        $this->accounts_create_sendmail = $accounts_create_sendmail;

        return $this;
    }

    /**
     *
     * @return int
     */
    public function is_accounts_create_auto()
    {
        return $this->accounts_create_auto;
    }

    /**
     *
     * @param int $accounts_create_auto
     *
     * @return $this
     */
    public function set_accounts_create_auto($accounts_create_auto)
    {
        $this->accounts_create_auto = $accounts_create_auto;

        return $this;
    }

    /**
     *
     * @return int
     */
    public function get_port()
    {
        return $this->port;
    }

    /**
     *
     * @param int $port
     *
     * @return $this
     */
    public function set_port($port)
    {
        $this->port = $port;

        return $this;
    }

    /**
     *
     * @return string
     */
    public function get_handler()
    {
        return $this->handler;
    }

    /**
     *
     * @param string $handler
     *
     * @return $this
     */
    public function set_handler($handler)
    {
        $this->handler = $handler;

        return $this;
    }

    /**
     */
    public function get_library_uri()
    {
        return $this->get_protocol() . '://' . $this->get_api_subdomain() . '.api.' . $this->get_domain() . '/socialize/library.js';
    }

    /**
     * Returns oneall main domain
     *
     * @return string
     */
    public function get_domain()
    {
        return 'oneall.com';
    }

    /**
     * Retuns protocol to use depending the configured port.
     *
     * @return string
     */
    public function get_protocol()
    {
        $protocol = 'http';
        if ($this->get_port() == 443)
        {
            $protocol .= 's';
        }

        return $protocol;
    }
}
