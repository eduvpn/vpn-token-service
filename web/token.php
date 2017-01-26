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
use SURFnet\VPN\Token\Http\Request;
use SURFnet\VPN\Token\Http\TokenResponse;
use SURFnet\VPN\Token\Token;

try {
    $configData = require sprintf('%s/config/config.php', dirname(__DIR__));
    $tokenStorage = new TokenStorage(new PDO(sprintf('sqlite:%s/data/db.sqlite', dirname(__DIR__))));

    // client "database"
    $getClientInfo = function ($clientId) use ($configData) {
        if (!array_key_exists('clientList', $configData)) {
            return false;
        }
        if (!array_key_exists($clientId, $configData['clientList'])) {
            return false;
        }

        return $configData['clientList'][$clientId];
    };

    // server
    $oauthServer = new OAuthServer(
        $tokenStorage,
        new Random(),
        new DateTime(),
        $getClientInfo
    );
    $oauthServer->setSignatureKeyPair(base64_decode($configData['signatureKeyPair']));

    $token = new Token($oauthServer);
    $token->run(new Request($_SERVER, $_GET, $_POST))->send();
} catch (Exception $e) {
    $response = new TokenResponse(
        500,
        [],
        ['error' => 'server_error', 'error_description' => $e->getMessage()]
    );
    $response->send();
}
