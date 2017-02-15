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
$configData = require sprintf('%s/config/config.php', dirname(__DIR__));
$keyPair = $configData['keyPair'];
$publicKey = base64_encode(
    \Sodium\crypto_sign_publickey(
        base64_decode($keyPair)
    )
);

echo $publicKey.PHP_EOL;
