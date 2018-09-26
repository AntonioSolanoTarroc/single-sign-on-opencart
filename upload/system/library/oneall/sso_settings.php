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
 * Class sso_settings Used to handle out specific sso settings
 *
 * @package Oneall
 */
class sso_settings
{
    /**
     * API Subdomain.
     *
     * @var string
     */
    protected $api_subdomain;

    /**
     * API Public Key.
     *
     * @var string
     */
    protected $api_public_key;

    /**
     * API Private Key.
     *
     * @var string
     */
    protected $api_private_key;

    /**
     * API Handler.
     *
     * @var string
     */
    protected $api_handler;

    /**
     * API Port.
     *
     * @var int
     */
    protected $api_port;

    /**
     * Automatically create accounts?
     *
     * @var int
     */
    protected $accounts_create_auto;

    /**
     * Send email to new customers?
     *
     * @var int
     */
    protected $accounts_create_sendmail;

    /**
     * Automatically link accounts?
     *
     * @var int
     */
    protected $accounts_link_automatic;

    /**
     * Link using unverified emails?
     *
     * @var int
     */
    protected $accounts_link_unverified;

    /**
     * SSO Session Lifetime.
     *
     * @var int
     */
    protected $session_lifetime;

    /**
     * SSO Session Top Realm.
     *
     * @var string
     */
    protected $session_realm;

    /**
     * SSO Session Sub Realm.
     *
     * @var string
     */
    protected $session_subrealm;

    /**
     * Unique identifier of this platform.
     *
     * @var string
     */
    protected $uniqid;

    /**
     * sso_settings constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        // Loop through settings.
        foreach ($config as $property => $value)
        {
            if (preg_match('#^module_oneallsso_(.+)$#i', $property, $matches))
            {
                if (property_exists($this, $matches[1]))
                {
                    $this->{$matches[1]} = $value;
                }
            }
        }
    }

    /**
     * Returns the unique identifier of this shop.
     *
     * @return string
     */
    public function get_uniqid()
    {
        return $this->api_subdomain;
    }

    /**
     * Sets the unique identifier of this shop.
     *
     * @param string $api_subdomain
     *
     * @return $this
     */
    public function set_uniqid($uniqid)
    {
        $this->uniqid = $uniqiq;
        return $this;
    }

    /**
     * Returns the API subdomain.
     *
     * @return string
     */
    public function get_api_subdomain()
    {
        return $this->api_subdomain;
    }

    /**
     * Sets the API subdomain.
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
    public function get_api_private_key()
    {
        return $this->api_private_key;
    }

    /**
     *
     * @param string $api_private_key
     *
     * @return $this
     */
    public function set_api_private_key($api_private_key)
    {
        $this->api_private_key = $api_private_key;

        return $this;
    }

    /**
     *
     * @return string
     */
    public function get_api_public_key()
    {
        return $this->api_public_key;
    }

    /**
     *
     * @param string $api_public_key
     *
     * @return $this
     */
    public function set_api_public_key($api_public_key)
    {
        $this->api_public_key = $api_public_key;

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
    public function get_api_port()
    {
        return $this->api_port;
    }

    /**
     *
     * @param int $api_port
     *
     * @return $this
     */
    public function set_api_port($api_port)
    {
        $this->api_port = $api_port;

        return $this;
    }

    /**
     *
     * @return string
     */
    public function get_api_handler()
    {
        return $this->api_handler;
    }

    /**
     *
     * @param string $api_handler
     *
     * @return $this
     */
    public function set_api_handler($api_handler)
    {
        $this->api_handler = $api_handler;

        return $this;
    }

    /**
     * Returns the complete uri of the OneAll library to include.
     *
     * @return string
     */
    public function get_library_uri()
    {
        return $this->get_protocol() . '://' . $this->get_api_subdomain() . '.api.' . $this->get_domain() . '/socialize/library.js';
    }

    /**
     * Returns the OneAll main domain.
     *
     * @return string
     */
    public function get_domain()
    {
        return 'oneall.com';
    }

    /**
     * Returns the protocol to use depending on the configured port.
     *
     * @return string
     */
    public function get_protocol()
    {
        return (($this->get_api_port() == 443) ? 'https' : 'http');
    }

}
