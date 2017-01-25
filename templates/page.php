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
    <table>
        <tr><th>Client ID</th><td><span title="{{ client_id }}">{{ display_name }}</span></td></tr>
        <tr><th>Redirect URI</th><td>{{ redirect_uri }}</td></tr>
        <tr><th>Scope</th><td>{{ scope }}</td></tr>
    </table>
    <form method="post">
        <fieldset>
            <button type="submit" name="approve" value="yes">Approve</button>
            <button type="submit" name="approve" value="no">Reject</button>
        </fieldset>
    </form>
</body>
</html>
EOF;
