<?php

/*
 * Copyright (C) 2019 Leda Ferreira
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace cognitive_services;

use Unirest\Request;
use Unirest\Response;
use cognitive_services\exception\ClientException;

/**
 * \cognitive_services\Client.
 */
class Client
{
    /**
     * @var string
     */
    const BASE_URL = 'https://[location].api.cognitive.microsoft.com/face/v1.0';

    /**
     * @var string
     */
    private $api_key;

    /**
     * @var string
     */
    private $region;

    /**
     * @var array
     */
    private $headers;

    /**
     * @var array
     */
    protected static $configuration;

    /**
     * @inheritdoc
     */
    public function __construct($api_key, $region)
    {
        $this->api_key = $api_key;
        $this->region = $region;
        $this->headers = [
            'Accept' => 'application/json',
            'Accept-Charset' => 'UTF-8',
            'Content-Type' => 'application/json',
            'User-Agent' => 'ledat/azure-face-api-client/0.1'
        ];

        Request::clearCurlOpts();
        Request::jsonOpts(true);
    }

    /**
     * Gets the base URL.
     * @return string
     */
    public function getBaseUrl()
    {
        return str_replace('[location]', $this->region, self::BASE_URL);
    }

    /**
     * Returns the authorization header.
     * @return array
     */
    private function getAuthorizationHeader()
    {
        return ['Ocp-Apim-Subscription-Key' => $this->api_key];
    }

    /**
     * Returns all headers, including the authorization header.
     * @return array
     */
    private function getHeaders()
    {
        return array_merge($this->getAuthorizationHeader(), $this->headers);
    }

    /**
     * Prepends the base url to the given path.
     * @param string $url
     * @return string
     */
    private function url($url)
    {
        if ($url{0} !== '/') {
            $url = "/{$url}";
        }
        return $this->getBaseUrl() . $url;
    }

    /**
     * Prepares the url parameters.
     * @param array $parameters
     * @return array
     */
    protected function prepare(array $parameters = [])
    {
        $params = [];
        foreach ($parameters as $key => $value) {
            if ($value === true) {
                $params[$key] = 'true';
            } elseif ($value === false) {
                $params[$key] = 'false';
            } elseif ($value !== null && $value !== '') {
                $params[$key] = strval($value);
            }
        }
        return $params;
    }

    /**
     * Creates a query string.
     * @param string $url
     * @param array $parameters
     * @return string
     */
    public function query($url = '', array $parameters = [])
    {
        if (($query = http_build_query($this->prepare($parameters)))) {
            return $url . '?' . $query;
        }

        return $url;
    }

    /**
     * Sends a GET request to a URL.
     * @param string $url
     * @param mixed $parameters
     * @return mixed
     */
    public function get($url, $parameters = null)
    {
        $response = Request::get(
            $this->url($url),
            $this->getHeaders(),
            $this->prepare($parameters)
        );

        return $this->process($response);
    }

    /**
     * Sends POST request to a URL.
     * @param string $url
     * @param mixed $body
     * @return mixed
     */
    public function post($url, $body = null)
    {
        $response = Request::post(
            $this->url($url),
            $this->getHeaders(),
            Request\Body::Json($body)
        );

        return $this->process($response);
    }

    /**
     * Sends a raw file upload request to a URL.
     * @param string $url
     * @param string $file
     * @return mixed
     */
    public function stream($url, $file)
    {
        $headers = $this->getHeaders();
        $headers['Content-Type'] = 'application/octet-stream';

        $response = Request::post(
            $this->url($url),
            $headers,
            Request\Body::Form(file_get_contents($file))
        );

        return $this->process($response);
    }

    /**
     * Sends a multipart/form-data upload request to a URL.
     * @param string $url
     * @param array $files
     * @param mixed $body
     * @return mixed
     */
    public function multipart($url, array $files, $body = null)
    {
        $headers = $this->getHeaders();
        $headers['Content-Type'] = 'multipart/form-data';

        $response = Request::post(
            $this->url($url),
            $headers,
            Request\Body::multipart($body, $files)
        );

        return $this->process($response);
    }

    /**
     * Sends DELETE request to a URL.
     * @param string $url
     * @return mixed
     */
    public function delete($url)
    {
        $response = Request::delete(
            $this->url($url),
            $this->getHeaders()
        );

        return $this->process($response);
    }

    /**
     * Sends PUT request to a URL.
     * @param string $url
     * @param mixed $body
     * @return mixed
     */
    public function put($url, $body = null)
    {
        $response = Request::put(
            $this->url($url),
            $this->getHeaders(),
            Request\Body::Json($body)
        );

        return $this->process($response);
    }

    /**
     * Sends PATCH request to a URL.
     * @param string $url
     * @param mixed $body
     * @return mixed
     */
    public function patch($url, $body = null)
    {
        $response = Request::patch(
            $this->url($url),
            $this->getHeaders(),
            Request\Body::Json($body)
        );

        return $this->process($response);
    }

    /**
     * Checks the webservice response for errors.
     * @param Response $response
     * @return mixed
     * @throws Exception
     */
    private function process(Response $response)
    {
        if (intval($response->code / 200) !== 1) {
            $code = $response->body['error']['code'] ?? $response->code;
            $message = "{$code}: {$response->body['error']['message']}";
            throw new ClientException($message, $response->code);
        }
        return $response->body;
    }

    /**
     * Client initialization and configuration.
     * @param string $api_key
     * @param string $region
     */
    public static function init($api_key, $region)
    {
        self::$configuration = [$api_key, $region];
    }

    /**
     * Returns a new instance of this client.
     * @return \cognitive_services\Client
     * @throws ClientException
     */
    public static function create()
    {
        if (!self::$configuration) {
            throw new ClientException('Client has not been initialized.');
        }

        list($api_key, $region) = self::$configuration;
        return new Client($api_key, $region);
    }
}
