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

use fkooman\OAuth\Server\Exception\OAuthException;
use fkooman\OAuth\Server\OAuthServer;
use fkooman\OAuth\Server\Random;
use fkooman\OAuth\Server\TokenStorage;
use SURFnet\VPN\Token\Template;

$templateManager = new Template(sprintf('%s/templates', dirname(__DIR__)));

try {
    $configData = require sprintf('%s/config/config.php', dirname(__DIR__));

    // storage
    $tokenStorage = new TokenStorage(new PDO(sprintf('sqlite:%s/data/db.sqlite', dirname(__DIR__))));
    $tokenStorage->init();

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

    // XXX take this from $_SERVER variable
    $userId = 'foo';

    $oauthServer->setSignatureKeyPair(base64_decode($configData['signatureKeyPair']));

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            $authorizeVariables = $oauthServer->getAuthorize($_GET);
            http_response_code(200);
            echo $templateManager->render('page', $authorizeVariables);
            break;
        case 'POST':
            $redirectUri = $oauthServer->postAuthorize($_GET, $_POST, $userId);
            http_response_code(302);
            header(sprintf('Location: %s', $redirectUri));
            break;
        default:
            http_response_code(405);
            header('Allow: GET,POST');
            echo $templateManager->render(
                'error',
                [
                    'errorCode' => 405,
                    'errorMessage' => 'Method Not Allowed',
                    'errorDescription' => '',
                ]
            );
            break;
    }
} catch (OAuthException $e) {
    http_response_code($e->getCode());
    echo $templateManager->render(
        'error',
        [
            'errorCode' => $e->getCode(),
            'errorMessage' => $e->getMessage(),
            'errorDescription' => $e->getDescription(),
        ]
    );
} catch (Exception $e) {
    http_response_code(500);
    echo $templateManager->render(
        'error',
        [
            'errorCode' => 500,
            'errorMessage' => 'Internal Server Error',
            'errorDescription' => $e->getMessage(),
        ]
    );
}
