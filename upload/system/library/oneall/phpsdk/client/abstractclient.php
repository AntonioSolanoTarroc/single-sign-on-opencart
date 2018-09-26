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
namespace Oneall\Phpsdk\Client;

abstract class AbstractClient implements ClientInterface
{
    /**
     * OneAll site, Subdomain
     *
     * @var string
     */
    protected $subDomain;

    /**
     * OneAll site, Base domain.
     *
     * @var string
     */
    protected $baseDomain;

    /**
     * Connection timeout in seconds.
     *
     * @var int
     */
    protected $timeout = 30;

    /**
     * OneAll site, Public key
     *
     * @var string
     */
    protected $publicKey;

    /**
     * OneAll site, Private key.
     *
     * @var string
     */
    protected $privateKey;

    /**
     * Use secure way to handle request? (HTTPS/SSL)
     *
     * @var boolean
     */
    protected $isSecure = true;

    /**
     * User agent for HTTP connections.
     *
     * @var string
     */
    protected $userAgent = 'SingleSignOn/3.1.0 OpenCart/3.x (+http://www.oneall.com/)';

    /**
     * AbstractClient constructor.
     *
     * @param string $subDomain
     * @param string $sitePublicKey
     * @param string $sitePrivateKey
     * @param bool $isSecure
     * @param string $base
     */
    public function __construct($subDomain, $sitePublicKey, $sitePrivateKey, $isSecure = true, $base = 'oneall.com')
    {
        $this->subDomain = $subDomain;
        $this->publicKey = $sitePublicKey;
        $this->privateKey = $sitePrivateKey;
        $this->isSecure = $isSecure;
        $this->baseDomain = $base;
    }

    /**
     *
     * @return string Client name identifier
     */
    abstract public function getName();

    /**
     * Scheme to use for the request
     *
     * @return string
     */
    abstract public function getScheme();

    /**
     *
     * @return string
     */
    public function getSubDomain()
    {
        return $this->subDomain;
    }

    /**
     *
     * @return string
     */
    public function getBaseDomain()
    {
        return $this->baseDomain;
    }

    /**
     *
     * @param string $baseDomain
     *
     * @return $this
     */
    public function setBaseDomain($baseDomain)
    {
        $this->baseDomain = $baseDomain;

        return $this;
    }

    /**
     *
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     *
     * @param int $timeout
     *
     * @return $this
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getHost()
    {
        $scheme = '';

        if ($this->getScheme())
        {
            $scheme = $this->getScheme() . '://';
        }

        return $scheme . $this->getDomain();
    }

    /**
     *
     * @return string
     */
    public function getDomain()
    {
        return $this->getSubDomain() . '.' . $this->getBaseDomain();
    }

    /**
     *
     * @return string
     */
    public function getPublicKey()
    {
        return $this->publicKey;
    }

    /**
     *
     * @return string
     */
    public function getPrivateKey()
    {
        return $this->privateKey;
    }

    /**
     *
     * @return bool
     */
    public function isSecure()
    {
        return $this->isSecure;
    }

    /**
     *
     * @param bool $isSecure
     *
     * @return $this
     */
    public function setIsSecure($isSecure)
    {
        $this->isSecure = $isSecure;

        return $this;
    }

    /**
     *
     * @return string
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     *
     * @param string $userAgent
     *
     * @return $this
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;
        return $this;
    }

}
