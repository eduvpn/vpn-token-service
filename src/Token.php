<?php
/**
 *  Copyright (C) 2017 SURFnet.
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

namespace SURFnet\VPN\Token;

use fkooman\OAuth\Server\Exception\OAuthException;
use fkooman\OAuth\Server\OAuthServer;
use SURFnet\VPN\Token\Http\Request;
use SURFnet\VPN\Token\Http\TokenResponse;

class Token
{
    /** @var \fkooman\OAuth\Server\OAuthServer */
    private $server;

    public function __construct(OAuthServer $server)
    {
        $this->server = $server;
    }

    /**
     * @return TokenResponse
     */
    public function run(Request $request)
    {
        try {
            if ('POST' === $request->getMethod()) {
                return new TokenResponse(
                    200,
                    [],
                    $this->server->postToken($request->getPostParameters(), $request->getAuthUser(), $request->getAuthPass())
                );
            }

            return new TokenResponse(
                405,
                [
                    'Allow' => 'POST',
                ],
                ['error' => 'invalid_request', 'error_description' => 'Method Not Allowed']
            );
        } catch (OAuthException $e) {
            $response = new TokenResponse(
                $e->getCode(),
                [],
                ['error' => $e->getMessage(), 'error_description' => $e->getDescription()]
            );
            if (401 === $e->getCode()) {
                $response->setHeader('WWW-Authenticate', 'Basic realm="OAuth"');
            }

            return $response;
        }
    }
}
