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

/**
 * \cognitive_services\ClientModel.
 */
class ClientModel extends Model
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @param mixed $attributes
     * @param \cognitive_services\Client $client
     */
    public function __construct($attributes = null, Client $client = null)
    {
        $this->setClient($client);
        parent::__construct($attributes ?: []);
    }

    /**
     * @param \cognitive_services\Client $client
     */
    private function setClient(Client $client = null)
    {
        $this->client = $client !== null ? $client : Client::create();
    }

    /**
     * @return \cognitive_services\Client
     */
    protected function getClient()
    {
        return $this->client;
    }

    /**
     * Gets the base URL.
     * @return string
     */
    protected function getBaseUrl()
    {
        return '';
    }

    /**
     * Prepends the base url to the given path.
     * @param string $url
     * @return string
     */
    private function url($url)
    {
        if ($url && $url{0} !== '/') {
            $url = "/{$url}";
        }
        return $this->getBaseUrl() . $url;
    }

    /**
     * Creates a query string.
     * @param string $url
     * @param array $parameters
     * @return string
     */
    protected function query($url, array $parameters = [])
    {
        return $this->client->query($url, $parameters);
    }

    /**
     * Sends a GET request to a URL.
     * @param string $url
     * @param mixed $parameters
     * @return mixed
     */
    protected function httpGet($url, $parameters = [])
    {
        return $this->client->get($this->url($url), $parameters);
    }

    /**
     * Sends POST request to a URL.
     * @param string $url
     * @param mixed $body
     * @return mixed
     */
    protected function httpPost($url, $body = null)
    {
        return $this->client->post($this->url($url), $body);
    }

    /**
     * Sends a raw file upload request to a URL.
     * @param string $url
     * @param string $file
     * @return mixed
     */
    protected function httpStream($url, $file)
    {
        return $this->client->stream($this->url($url), $file);
    }

    /**
     * Sends a multipart/form-data upload request to a URL.
     * @param string $url
     * @param array $files
     * @param mixed $body
     * @return mixed
     */
    protected function httpMultipart($url, array $files, $body = null)
    {
        return $this->client->multipart($this->url($url), $files, $body);
    }

    /**
     * Sends DELETE request to a URL.
     * @param string $url
     * @return mixed
     */
    protected function httpDelete($url)
    {
        return $this->client->delete($this->url($url));
    }

    /**
     * Sends PUT request to a URL.
     * @param string $url
     * @param mixed $body
     * @return mixed
     */
    protected function httpPut($url, $body = null)
    {
        return $this->client->put($this->url($url), $body);
    }

    /**
     * Sends PATCH request to a URL.
     * @param string $url
     * @param mixed $body
     * @return mixed
     */
    protected function httpPatch($url, $body = null)
    {
        return $this->client->patch($this->url($url), $body);
    }
}
