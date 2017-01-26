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

namespace SURFnet\VPN\Token;

class Response
{
    /** @var int */
    private $statusCode;

    /** @var array */
    private $headers;

    /** @var string */
    private $body;

    public function __construct($statusCode = 200, array $headers = [], $body = '')
    {
        $this->statusCode = (int) $statusCode;
        $this->headers = $headers;
        $this->body = (string) $body;
    }

    public function __toString()
    {
        $output = [];
        $output[] = sprintf('HTTP/1.1 %d %s', $this->statusCode, self::statusReason($this->statusCode));
        foreach ($this->headers as $k => $v) {
            $output[] = sprintf('%s: %s', $k, implode(',', $v));
        }
        $output[] = '';
        $output[] = $this->getBody();

        return implode("\r\n", $output);
    }

    public function setHeader($key, $value)
    {
        $this->headers[$key] = [$value];
    }

    public function addHeader($key, $value)
    {
        if (!array_key_exists($key, $this->headers)) {
            $this->headers[$key] = [];
        }
        $this->headers[$key][] = $value;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function setBody($body)
    {
        $this->body = $body;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function send()
    {
        http_response_code($this->statusCode);
        foreach ($this->headers as $k => $v) {
            header(sprintf('%s: %s', $k, implode(',', $v)));
        }
        echo $this->body;
    }

    private static function statusReason($statusCode)
    {
        $statusReasons = [
            100 => 'Continue',
            101 => 'Switching Protocols',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Payload Too Large',
            414 => 'URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Range Not Satisfiable',
            417 => 'Expectation Failed',
            426 => 'Upgrade Required',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
        ];

        if (!array_key_exists($statusCode, $statusReasons)) {
            return false;
        }

        return $statusReasons[$statusCode];
    }
}
