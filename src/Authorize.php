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
use SURFnet\VPN\Token\Http\AuthorizeResponse;
use SURFnet\VPN\Token\Http\Request;

class Authorize
{
    /** @var \fkooman\OAuth\Server\OAuthServer */
    private $server;

    /** @var TplInterface */
    private $tpl;

    public function __construct(OAuthServer $server, TplInterface $tpl)
    {
        $this->server = $server;
        $this->tpl = $tpl;
    }

    /**
     * @return AuthorizeResponse
     */
    public function run(Request $request, $userId)
    {
        try {
            if ('GET' === $request->getMethod()) {
                return new AuthorizeResponse(
                    200,
                    [],
                    $this->tpl->render(
                        'page',
                        $this->server->getAuthorize($request->getQueryParameters())
                    )
                );
            }

            if ('POST' === $request->getMethod()) {
                return new AuthorizeResponse(
                    302,
                    [
                        'Location' => $this->server->postAuthorize($request->getQueryParameters(), $request->getPostParameters(), $userId),
                    ]
                );
            }

            return new AuthorizeResponse(
                405,
                [
                    'Allow' => 'GET,POST',
                ],
                $this->tpl->render(
                    'error',
                    [
                        'errorCode' => 405,
                        'errorMessage' => 'Method Not Allowed',
                        'errorDescription' => '',
                    ]
                )
            );
        } catch (OAuthException $e) {
            return new AuthorizeResponse(
                $e->getCode(),
                [],
                $this->tpl->render(
                    'error',
                    [
                        'errorCode' => $e->getCode(),
                        'errorMessage' => $e->getMessage(),
                        'errorDescription' => $e->getDescription(),
                    ]
                )
            );
        }
    }
}
