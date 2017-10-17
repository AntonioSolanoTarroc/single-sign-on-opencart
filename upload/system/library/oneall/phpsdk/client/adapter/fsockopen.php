<?php

/**
 * @package   	OneAll SDK
 * @copyright 	Copyright 2017-Present http://www.oneall.com
 * @license   	GNU/GPL 2 or later
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
namespace Oneall\Phpsdk\Client\Adapter;

use Oneall\Phpsdk\Client\AbstractClient;
use Oneall\Phpsdk\Client\Response;

class FSockOpen extends AbstractClient
{
	const SCHEME_HTTPS = 'ssl';
	const SCHEME_HTTP = 'http';

	/**
	 *
	 * @ERROR!!!
	 *
	 */
	public function getName ()
	{
		return 'fsockopen';
	}

	/**
	 *
	 * @ERROR!!!
	 *
	 */
	public function get ($path, $options = array())
	{
		return $this->request ($path, 'GET', null, $options);
	}

	/**
	 *
	 * @ERROR!!!
	 *
	 */
	public function post ($path, array $data, array $options = array())
	{
		return $this->request ($path, 'POST', $data, $options);
	}

	/**
	 *
	 * @ERROR!!!
	 *
	 */
	public function put ($path, array $data, array $options = array())
	{
		return $this->request ($path, 'PUT', $data, $options);
	}

	/**
	 *
	 * @ERROR!!!
	 *
	 */
	public function delete ($path, $options = array())
	{
		return $this->request ($path, 'DELETE', null, $options);
	}

	/**
	 *
	 * @ERROR!!!
	 *
	 */
	public function getScheme ()
	{
		if ($this->isSecure ())
		{
			return self::SCHEME_HTTPS;
		}

		return '';
	}

	/**
	 *
	 * @return int
	 */
	protected function getPort ()
	{
		if ($this->isSecure ())
		{
			return 443;
		}

		return 80;
	}

	/**
	 *
	 * @param string $path
	 * @param string $method
	 *
	 * @return mixed
	 */
	protected function request ($path, $method, $data = null)
	{
		$errno = $errstr = null;

		// Create socket
		if (!$socket = fsockopen ($this->getHost (), $this->getPort (), $errno, $errstr, $this->getTimeout ()))
		{
			throw new \RuntimeException ('Error while opening fsokopen : [' . $errno . ']' . trim ($errstr));
		}

		if ($data)
		{
			$data = json_encode ($data);
		}

		// / Build request
		$parts = array ();

		// Method
		$parts['method'] = strtoupper ($method) . " " . $path . " HTTP/1.1";

		// Host
		$parts['host'] = "Host: " . $this->getHost ();

		// Authorization
		$authorization = $this->getAutorization ();
		if ( ! empty ($authorization))
		{
			$parts['authorization'] = "Authorization: Basic " . $authorization;
		}

		// Agent
		$userAgent = $this->getUserAgent ();
		if ( ! empty ($userAgent))
		{
			$parts['user-agent'] = "User-Agent: " . $authorization;
		}

		// Add POST data ?
		if ( ! empty ($data))
		{
			$parts['content-length'] = "Content-length: " . strlen ($data);
		}


		// Add POST data ?
		if ( ! empty ($data))
		{
			$parts['data'] = $data;
		}

		print_r(implode ($parts,"||"));
		// Fwrite
		fwrite ($socket, implode ($parts,"\r\n")."\r\n\r\n");

		// Fetch response
		$rawResponse = '';
		while ( !feof ($socket) )
		{
			$rawResponse .= fread ($socket, 1024);
		}
		print_r($rawResponse);
		// Close connection
		fclose ($socket);

		// Parse response
		list ($responseHeader, $responseBody) = explode ("\r\n\r\n", $rawResponse, 2);

		// Parse header
		$responseHeader = preg_split ("/\r\n|\n|\r/", $responseHeader);
		list ($headerProtocol, $headerCode, $headerStatusMessage) = explode (' ', trim (array_shift ($responseHeader)), 3);

		$response = new Response ($headerCode, $responseHeader, $responseBody, $headerProtocol, $headerStatusMessage);

		return $response;
	}

	/**
	 * Build authorization header
	 *
	 * @return string
	 */
	private function getAutorization ()
	{
		$publicKey = $this->getPublicKey ();
		$privateKey = $this->getPrivateKey ();

		if ( ! empty ($publicKey) && ! empty ($privateKey))
		{
			return base64_encode ($publicKey . ":" . $privateKey);
		}

		return null;
	}
}
