<?php
/**
 *  Copyright (C) 2017 François Kooman <fkooman@tuxed.net>.
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as
 *  published by the Free Software Foundation, either version 3 of the
 *  License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace SURFnet\VPN\Token\Http;

class Request
{
    /** @var array */
    private $serverData;

    /** @var array */
    private $getData;

    /** @var array */
    private $postData;

    /**
     * @param array $serverData
     * @param array $getData
     * @param array $postData
     */
    public function __construct(array $serverData, array $getData, array $postData)
    {
        $this->serverData = $serverData;
        $this->getData = $getData;
        $this->postData = $postData;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->serverData['REQUEST_METHOD'];
    }

    /**
     * @return array
     */
    public function getQueryParameters()
    {
        return $this->getData;
    }

    /**
     * @return array
     */
    public function getPostParameters()
    {
        return $this->postData;
    }

    /**
     * @return string|null
     */
    public function getAuthUser()
    {
        return $this->getHeader('PHP_AUTH_USER');
    }

    /**
     * @return string|null
     */
    public function getAuthPass()
    {
        return $this->getHeader('PHP_AUTH_PW');
    }

    /**
     * @return string|null
     */
    public function getHeader($key)
    {
        return array_key_exists($key, $this->serverData) ? $this->serverData[$key] : null;
    }

    /**
     * @return string
     */
    public function getAuthority()
    {
        $requestScheme = array_key_exists('REQUEST_SCHEME', $this->serverData) ? $this->serverData['REQUEST_SCHEME'] : 'http';
        $serverName = $this->serverData['SERVER_NAME'];
        $serverPort = (int) $this->serverData['SERVER_PORT'];
        if (('https' === $requestScheme && 443 !== $serverPort) || ('http' === $requestScheme && 80 !== $serverPort)) {
            return sprintf('%s://%s:%d', $requestScheme, $serverName, $serverPort);
        }

        return sprintf('%s://%s', $requestScheme, $serverName);
    }
}
