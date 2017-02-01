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
require_once sprintf('%s/vendor/autoload.php', dirname(__DIR__));

use fkooman\OAuth\Server\OAuthServer;
use fkooman\OAuth\Server\Random;
use fkooman\OAuth\Server\TokenStorage;
use SURFnet\VPN\Token\Authorize;
use SURFnet\VPN\Token\Config;
use SURFnet\VPN\Token\Http\AuthorizeResponse;
use SURFnet\VPN\Token\Http\Request;
use SURFnet\VPN\Token\Template;

$tpl = new Template(sprintf('%s/templates', dirname(__DIR__)));

try {
    $config = new Config(require sprintf('%s/config/config.php', dirname(__DIR__)));
    $tokenStorage = new TokenStorage(new PDO(sprintf('sqlite:%s/data/db.sqlite', dirname(__DIR__))));

    // client "database"
    $getClientInfo = function ($clientId) use ($config) {
        if (!isset($config->clientList)) {
            return false;
        }
        if (!isset($config->clientList->$clientId)) {
            return false;
        }

        return $config->clientList->$clientId->toArray();
    };

    // server
    $oauthServer = new OAuthServer(
        $tokenStorage,
        new Random(),
        new DateTime(),
        $getClientInfo
    );
    $oauthServer->setSignatureKeyPair(base64_decode($config->signatureKeyPair));

    if (!isset($config->userIdAttribute)) {
        throw new RuntimeException('"userIdAttribute" not set in configuration file');
    }

    if (!array_key_exists($config->userIdAttribute, $_SERVER)) {
        throw new RuntimeException('"userIdAttribute" not available as a server variable');
    }

    $userId = $_SERVER[$config->userIdAttribute];

    $authorize = new Authorize($oauthServer, $tpl);
    $authorize->run(new Request($_SERVER, $_GET, $_POST), $userId)->send();
} catch (Exception $e) {
    $response = new AuthorizeResponse(
        500,
        [],
        $tpl->render(
            'error',
            [
                'errorCode' => 500,
                'errorMessage' => 'Internal Server Error',
                'errorDescription' => $e->getMessage(),
            ]
        )
    );
    $response->send();
}
