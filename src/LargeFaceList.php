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
use cognitive_services\common\TrainingStatus;
use cognitive_services\exception\ModelException;
use cognitive_services\exception\UninitializedException;
use cognitive_services\face\FaceRectangle;

/**
 * Faces in a specified large face list.
 *
 * @property-read string $largeFaceListId
 * @property-read string $name
 * @property-read string $userData
 * @property-read string $recognitionModel
 */
class LargeFaceList extends ClientModel
{
    /**
     * {@inheritdoc}
     */
    protected function getBaseUrl()
    {
        return '/largefacelists' . (
            $this->largeFaceListId ? "/{$this->largeFaceListId}" : ''
        );
    }

    /**
     * Add a face to a specified large face list.
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
        if (!$this->largeFaceListId) {
            throw new UninitializedException('"largeFaceListId" is invalid.');
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
     * Create an empty large face list with user-specified largeFaceListId,
     * name, an optional userData and recognitionModel.
     *
     * @param string $largeFaceListId
     * @param string $name
     * @param string $userData
     * @param string $recognitionModel
     * @return LargeFaceList
     */
    public function create($largeFaceListId, $name, $userData = null, $recognitionModel = null)
    {
        $this->httpPut("/{$largeFaceListId}", array_filter([
            'name' => $name,
            'userData' => $userData,
            'recognitionModel' => $recognitionModel,
        ]));

        return new LargeFaceList([
            'largeFaceListId' => $largeFaceListId,
            'name' => $name,
            'userData' => $userData,
            'recognitionModel' => $recognitionModel,
        ], $this->getClient());
    }

    /**
     * Delete a specified face list.
     *
     * @return void
     * @throws UninitializedException
     */
    public function delete()
    {
        if (!$this->largeFaceListId) {
            throw new UninitializedException('"largeFaceListId" is invalid.');
        }

        $this->httpDelete('');
    }

    /**
     * Delete a face from a face list by specified faceListId and persistedFaceId.
     *
     * @param string $persistedFaceId
     * @return void
     * @throws UninitializedException
     */
    public function deleteFace($persistedFaceId)
    {
        if (!$this->largeFaceListId) {
            throw new UninitializedException('"largeFaceListId" is invalid.');
        }

        $this->httpDelete("/persistedFaces/{$persistedFaceId}");
    }

    /**
     * Retrieve a face list’s faceListId, name, userData,
     * recognitionModel and faces in the face list.
     *
     * @param string $largeFaceListId
     * @param string $returnRecognitionModel
     * @return \cognitive_services\LargeFaceList
     */
    public function get($largeFaceListId, $returnRecognitionModel = null)
    {
        return new LargeFaceList(
            $this->httpGet("/{$largeFaceListId}", array_filter([
                'returnRecognitionModel' => $returnRecognitionModel,
            ])),
            $this->getClient()
        );
    }

    /**
     * Retrieve persisted face in large face list by largeFaceListId and persistedFaceId.
     *
     * @param string $persistedFaceId
     * @return PersistedFace
     * @throws UninitializedException
     */
    public function getFace($persistedFaceId)
    {
        if (!$this->largeFaceListId) {
            throw new UninitializedException('"largeFaceListId" is invalid.');
        }

        return new PersistedFace(
            $this->httpGet("/persistedfaces/{$persistedFaceId}")
        );
    }

    /**
     * Checks if the large face list's training status is completed or still ongoing.
     *
     * @return TrainingStatus
     * @throws UninitializedException
     */
    public function getTrainingStatus()
    {
        if (!$this->largeFaceListId) {
            throw new UninitializedException('"largeFaceListId" is invalid.');
        }

        return new TrainingStatus(
            $this->httpGet("/training")
        );
    }

    /**
     * List face lists’ faceListId, name, userData and recognitionModel.
     *
     * @param string $start
     * @param integer $top
     * @param boolean $returnRecognitionModel
     * @return \cognitive_services\LargeFaceList[]
     */
    public function list($start = null, $top = null, $returnRecognitionModel = null)
    {
        $result = $this->httpGet('', array_filter([
            'start' => $start,
            'top' => $top,
            'returnRecognitionModel' => $returnRecognitionModel,
        ]));

        $lists = [];
        foreach ($result as $item) {
            $lists[] = new LargeFaceList($item, $this->getClient());
        }
        return $lists;
    }

    /**
     * List faces' persistedFaceId and userData in a specified large face list.
     *
     * @param string $start
     * @param integer $top
     * @return PersistedFace[]
     * @throws UninitializedException
     */
    public function listFace($start = null, $top = null)
    {
        if (!$this->largeFaceListId) {
            throw new UninitializedException('"largeFaceListId" is invalid.');
        }

        $result = $this->httpGet('/persistedfaces', array_filter([
            'start' => $start,
            'top' => $top,
        ]));

        $faces = [];
        foreach ($result as $item) {
            $faces[] = new PersistedFace($item);
        }
        return $faces;
    }

    /**
     * Submit a large face list training task.
     *
     * @return void
     * @throws UninitializedException
     */
    public function train()
    {
        if (!$this->largeFaceListId) {
            throw new UninitializedException('"largeFaceListId" is invalid.');
        }

        $this->httpPost('/train');
    }

    /**
     * Update information of a face list, including name and userData.
     *
     * @param string $name
     * @param string $userData
     * @return void
     * @throws UninitializedException
     */
    public function update($name, $userData)
    {
        if (!$this->largeFaceListId) {
            throw new UninitializedException('"largeFaceListId" is invalid.');
        }

        $this->httpPatch('', array_filter([
            'name' => $name,
            'userData' => $userData,
        ]));
    }

    /**
     * Update a specified face's userData field in a large face list by its persistedFaceId.
     *
     * @param string $persistedFaceId
     * @param string $userData
     * @return void
     * @throws UninitializedException
     */
    public function updateFace($persistedFaceId, $userData)
    {
        if (!$this->largeFaceListId) {
            throw new UninitializedException('"largeFaceListId" is invalid.');
        }

        $this->httpPatch("/persistedfaces/{$persistedFaceId}", array_filter([
            'userData' => $userData,
        ]));
    }
}
