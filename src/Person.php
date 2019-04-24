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

use cognitive_services\common\PersistedFace;
use cognitive_services\exception\UninitializedException;
use cognitive_services\face\FaceRectangle;

/**
 * \cognitive_services\Person.
 *
 * @property-read string $personId
 * @property-read array $persistedFaceIds
 * @property-read string $name
 * @property-read string $userData
 */
class Person extends ClientModel
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $groupId;

    /**
     * @var string
     */
    private $personGroupId;

    /**
     * @var string
     */
    private $largePersonGroupId;

    /**
     * @param array $attributes
     * @param string $personGroupId
     * @param string $largePersonGroupId
     * @param \cognitive_services\Client $client
     */
    public function __construct(
        $attributes = null,
        $personGroupId = null,
        $largePersonGroupId = null,
        Client $client = null
    ) {
        parent::__construct($attributes, $client);

        if (!$personGroupId && !$largePersonGroupId) {
            throw new ModelException('"personGroupId" and "largePersonGroupId" are invalid.');
        } elseif ($personGroupId) {
            $this->path = 'persongroups';
            $this->groupId = $personGroupId;
            $this->personGroupId = $personGroupId;

        } else {
            $this->path = 'largepersongroups';
            $this->groupId = $largePersonGroupId;
            $this->largePersonGroupId = $largePersonGroupId;
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getBaseUrl()
    {
        return "/{$this->path}/{$this->groupId}/persons" . (
            $this->personId ? "/{$this->personId}" : ''
        );
    }

    /**
     * Add a face to a person into a (large) person group
     * for face identification or verification.
     *
     * @param string $url
     * @param string $userData
     * @param FaceRectangle|array|string $targetFace
     * @return PersistedFace
     * @throws UninitializedException
     * @throws \InvalidArgumentException
     */
    public function addFace($url, $userData = null, $targetFace = null)
    {
        if (!$this->personId) {
            throw new UninitializedException('"personId" is invalid.');
        }

        if ($targetFace instanceof FaceRectangle) {
            $rectangle = $targetFace->asParam();
        } elseif (is_array($targetFace)) {
            $rectangle = implode(',', $targetFace);
        } else {
            $rectangle = $targetFace;
        }

        $endpoint = $this->query('/persistedFaces', [
            'userData' => $userData,
            'targetFace' => $rectangle,
        ]);

        if (is_remote_url($url)) {
            $result = $this->httpPost($endpoint, ['url' => $url]);
        } elseif (is_local_file($url)) {
            $result = $this->httpStream($endpoint, $url);
        } else {
            throw new ModelException('"url" is not a valid URL or a valid local file.');
        }

        return new PersistedFace(
            array_merge($result, ['userData' => $userData])
        );
    }

    /**
     * Create a new person in a specified (large) person group.
     *
     * @param string $name
     * @param string $userData
     * @return Person
     */
    public function create($name, $userData = null)
    {
        $result = $this->httpPost('', array_filter([
            'name' => $name,
            'userData' => $userData,
        ]));

        return new Person(
            array_merge($result, [
                'name' => $name,
                'userData' => $userData,
            ]),
            $this->personGroupId,
            $this->largePersonGroupId,
            $this->getClient()
        );
    }

    /**
     * Delete an existing person from a (large) person group.
     *
     * @return void
     * @throws UninitializedException
     */
    public function delete()
    {
        if (!$this->personId) {
            throw new UninitializedException('"personId" is invalid.');
        }

        $this->httpDelete('');
    }

    /**
     * Delete a face from a person in a (large) person group.
     *
     * @param string $persistedFaceId
     * @return void
     * @throws UninitializedException
     */
    public function deleteFace($persistedFaceId)
    {
        if (!$this->personId) {
            throw new UninitializedException('"personId" is invalid.');
        }

        $this->httpDelete("/persistedFaces/{$persistedFaceId}");
    }

    /**
     * Retrieve a person's name and userData, and the persisted faceIds.
     *
     * @param string $personId
     * @return \cognitive_services\Person
     */
    public function get($personId)
    {
        return new Person(
            $this->httpGet("/{$personId}"),
            $this->personGroupId,
            $this->largePersonGroupId,
            $this->getClient()
        );
    }

    /**
     * Retrieve person face information.
     *
     * @param string $persistedFaceId
     * @return PersistedFace
     * @throws UninitializedException
     */
    public function getFace($persistedFaceId)
    {
        if (!$this->personId) {
            throw new UninitializedException('"personId" is invalid.');
        }

        return new PersistedFace(
            $this->httpGet("/persistedFaces/{$persistedFaceId}")
        );
    }

    /**
     * List all persons’ information in the specified (large) person group,
     * including personId, name, userData and persistedFaceIds of registered
     * person faces.
     *
     * @param string $start
     * @param integer $top
     * @return \cognitive_services\Person
     */
    public function list($start = null, $top = null)
    {
        $result = $this->httpGet('', array_filter([
            'start' => $start,
            'top' => $top,
        ]));

        $lists = [];
        foreach ($result as $item) {
            $lists[] = new Person(
                $item,
                $this->personGroupId,
                $this->largePersonGroupId,
                $this->getClient()
            );
        }
        return $lists;
    }

    /**
     * Update name or userData of a person.
     *
     * @param string $name
     * @param string $userData
     * @throws UninitializedException
     */
    public function update($name, $userData)
    {
        if (!$this->personId) {
            throw new UninitializedException('"personId" is invalid.');
        }

        $this->httpPatch('', array_filter([
            'name' => $name,
            'userData' => $userData,
        ]));
    }

    /**
     * Update a person persisted face's userData field.
     *
     * @param string $persistedFaceId
     * @param string $userData
     * @throws UninitializedException
     */
    public function updateFace($persistedFaceId, $userData)
    {
        if (!$this->personId) {
            throw new UninitializedException('"personId" is invalid.');
        }

        $this->httpPatch("/persistedFaces/{$persistedFaceId}", array_filter([
            'userData' => $userData,
        ]));
    }
}
