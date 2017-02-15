#!/usr/bin/env php
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

use fkooman\OAuth\Server\Storage;

try {
    // initialize the DB
    $storage = new Storage(new PDO(sprintf('sqlite:%s/data/db.sqlite', dirname(__DIR__))));
    $storage->init();

    // set the OAuth keypair
    $configFile = sprintf('%s/config/config.php', dirname(__DIR__));
    $configData = require $configFile;
    $configData['keyPair'] = base64_encode(\Sodium\crypto_sign_keypair());
    $fileContent = sprintf('<?php return %s;', var_export($configData, true));
    if (false === @file_put_contents($configFile, $fileContent)) {
        throw new RuntimeException(sprintf('unable to write "%s"', $configFile));
    }
} catch (Exception $e) {
    echo sprintf('ERROR: %s', $e->getMessage()).PHP_EOL;
    exit(1);
}
