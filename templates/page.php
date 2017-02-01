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
return <<< 'EOF'
<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width; height=device-height; initial-scale=1">
    <title>Authorize</title>
    <link rel="stylesheet" type="text/css" href="css/screen.css">
</head>
<body>
    <h1>Authorize</h1>
    <p>
You can authorize the application below to manage your VPN configurations! If you
do NOT recognize the application, do NOT click "Approve".
    </p>
    <table>
        <tr><th>Application</th><td><span title="{{ client_id }}">{{ display_name }}</span></td></tr>
        <tr><th>Redirect URI</th><td><code>{{ redirect_uri }}</code></td></tr>
        <tr><th>Permissions</th><td><code>{{ scope }}</code></td></tr>
    </table>
    <form method="post">
        <fieldset>
            <button class="reject" type="submit" name="approve" value="no">Reject</button>
            <button class="approve" type="submit" name="approve" value="yes">Approve</button>
        </fieldset>
    </form>
</body>
</html>
EOF;
