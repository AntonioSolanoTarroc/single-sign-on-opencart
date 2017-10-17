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
namespace Oneall;

use Oneall\Database\Sso;
use Oneall\Phpsdk\Client\Builder;
use Oneall\Phpsdk\OneallApi;

/**
 * Class AbstractOneallSsoController
 *
 * @package Oneall
 */
class AbstractOneallSsoController extends \Controller
{
	/**
	 * Sso settings (those save from admin panel)
	 *
	 * @var \Oneall\sso_settings
	 */
	protected $settings;

	/**
	 *
	 * @var \Oneall\Phpsdk\OneallApi
	 */
	protected $api;

	/**
	 *
	 * @var \Oneall\ApiFacade
	 */
	protected $facade;

	/**
	 *
	 * @var \Oneall\Database\Sso
	 */
	protected $ssoDatabase;

	/**
	 *
	 * @var \Oneall\DataExporter
	 */
	protected $exporter;

	/**
	 *
	 * @var \Oneall\SessionStorage
	 */
	protected $storage;
	/**
	 *
	 * @var \Oneall\Synchronizer
	 */
	protected $synchronizer;

	/**
	 * AbstractOneallSsoController constructor.
	 *
	 * @param
	 *        	$registry
	 */
	public function __construct ($registry)
	{
		parent::__construct ($registry);

		$this->storage = new SessionStorage ($this->session);

		// Load Language
		$this->load->language ('extension/module/oneall');

		// Load settings
		$this->load->model ('setting/setting');
		$this->settings = new sso_settings ($this->model_setting_setting->getSetting ('oasso'));

		// build client handler
		$client = $this->buildClient ($this->settings);
		$this->api = new OneallApi ($client);

		// adding required scripts
		$this->document->addScript ($this->settings->get_library_uri ());

		if ($this->storage->getSessionToken ())
		{
			$this->addSsoLibrary ($this->storage->getSessionToken ());
		}

		$this->facade = new \Oneall\ApiFacade ($this->api, $this->log);

		$this->load->model ('account/customer');

		$accountCustomer = new \ModelAccountCustomer ($this->registry);

		// Delegate sql queries to ssoDatabase objects
		$this->ssoDatabase = new Sso ($this->db, $accountCustomer, $this->config);
		$this->exporter = new DataExporter ($this->ssoDatabase);
		$this->synchronizer = new Synchronizer ($this->ssoDatabase, $this->api, $this->facade, $this->exporter, $this->settings);

		return true;
	}

	/**
	 *
	 * @param \Oneall\sso_settings $settings
	 *
	 * @return \Oneall\Phpsdk\Client\AbstractClient
	 */
	private function buildClient (sso_settings $settings)
	{
		$builder = new Builder ();
		$client = $builder->build ($settings->get_handler (), $settings->get_api_subdomain (), $settings->get_public_key (), $settings->get_private_key (), $settings->get_port () == 443, 'api.' . $settings->get_domain ());

		return $client;
	}

	/**
	 * Create a new sso session on all required server for the given user id.
	 *
	 * @param int $customerId
	 */
	protected function startSession ($customerId)
	{
		$identityToken = $this->ssoDatabase->getIdentityToken ($customerId);

		// start a new session on oneall servers
		$response = $this->api->startIdentitySession ($identityToken);

		// Get & store the sso session token
		$sessionToken = $this->facade->getSsoSessionToken ($identityToken);
		$this->storage->storeSessionToken ($sessionToken);

		// add js to create sso cookie.
		$this->document->addScript ('catalog/view/javascript/oneall/sso_start_session.js?sso_token=' . $identityToken);
	}

	/**
	 *
	 * @param string|null $sessionToken
	 *
	 * @return null
	 */
	protected function addSsoLibrary ($sessionToken = null)
	{
		$this->removeSsoLibrary ();
		$suffix = '';
		if ($sessionToken)
		{
			$suffix = '?sso_session_token=' . $sessionToken;
		}

		// add js to create sso cookie.
		$this->document->addScript ('catalog/view/javascript/oneall/sso_library.js' . $suffix);

		return null;
	}

    /**
     *
     */
	protected function removeSsoLibrary ()
	{
		// we'll first remove sso library from document scripts
		$propertyReflection = new \ReflectionProperty ($this->document, 'scripts');
		$propertyReflection->setAccessible (true);
		$scripts = $propertyReflection->getValue ($this->document);

		foreach ($scripts ['header'] as $key => $script)
		{
			if (strpos ($script, 'oneall/sso_library.js') > 0)
			{
				unset ($scripts ['header'] [$key]);
			}
		}

		$propertyReflection = new \ReflectionProperty ($this->document, 'scripts');
		$propertyReflection->setAccessible (true);
		$propertyReflection->setValue ($this->document, $scripts);
	}
}
