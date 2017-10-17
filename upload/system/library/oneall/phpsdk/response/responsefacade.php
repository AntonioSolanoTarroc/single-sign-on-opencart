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

namespace Oneall\Phpsdk\Response;

class ResponseFacade extends AbstractResponseFacade
{

    /**
     *
     * @return string
     */
    public function getUserEmail()
    {
        $identities = $this->getObjectValue('/response/result/data/user/identities');

        foreach ($identities as $identity)
        {
            if (!isset ($identity->emails) || !is_array($identity->emails))
            {
                continue;
            }

            foreach ($identity->emails as $email)
            {
                if ($email->value && (!property_exists($email, 'is_valid') || $email->is_valid !== false))
                {
                    return $email->value;
                }
            }
        }

        return null;
    }

    /**
     *
     * @return string
     */
    public function getUserToken()
    {
        return $this->getObjectValue('response/result/data/user/user_token');
    }

    /**
     *
     * @return string
     */
    public function getIdentityToken()
    {
        return $this->getObjectValue('response/result/data/user/identity/identity_token');
    }

    /**
     *
     * @return array
     */
    public function getIdentities()
    {
        return $this->getObjectValue('response/result/data/user/identities');
    }

    /**
     *
     * @return string
     */
    public function getProvider()
    {
        return $this->getObjectValue('response/result/data/user/identity/provider');
    }
}
