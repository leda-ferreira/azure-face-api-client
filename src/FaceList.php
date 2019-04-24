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
use cognitive_services\exception\ModelException;
use cognitive_services\exception\UninitializedException;
use cognitive_services\face\FaceRectangle;

/**
 * \cognitive_services\FaceList.
 *
 * @property-read string $faceListId
 * @property-read string $name
 * @property-read string $userData
 * @property-read string $recognitionModel
 * @property-read array $persistedFaces
 */
class FaceList extends ClientModel
{
    /**
     * {@inheritdoc}
     */
    protected $populateListMap = [
        'persistedFaces' => PersistedFace::class,
    ];

    /**
     * {@inheritdoc}
     */
    protected function getBaseUrl()
    {
        return '/facelists' . (
            $this->faceListId ? "/{$this->faceListId}" : ''
        );
    }

    /**
     * Add a face to a specified face list.
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
        if (!$this->faceListId) {
            throw new UninitializedException('"faceListId" is invalid.');
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
     * Create an empty face list with user-specified faceListId,
     * name, an optional userData and recognitionModel.
     *
     * @param string $faceListId
     * @param string $name
     * @param string $userData
     * @param string $recognitionModel
     * @return FaceList
     */
    public function create($faceListId, $name, $userData = null, $recognitionModel = null)
    {
        $this->httpPut("/{$faceListId}", array_filter([
            'name' => $name,
            'userData' => $userData,
            'recognitionModel' => $recognitionModel,
        ]));

        return new FaceList([
            'faceListId' => $faceListId,
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
        if (!$this->faceListId) {
            throw new UninitializedException('"faceListId" is invalid.');
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
        if (!$this->faceListId) {
            throw new UninitializedException('"faceListId" is invalid.');
        }

        $this->httpDelete("/persistedFaces/{$persistedFaceId}");
    }

    /**
     * Retrieve a face list’s faceListId, name, userData,
     * recognitionModel and faces in the face list.
     *
     * @param string $faceListId
     * @param string $returnRecognitionModel
     * @return \cognitive_services\FaceList
     */
    public function get($faceListId, $returnRecognitionModel = null)
    {
        return new FaceList(
            $this->httpGet("/{$faceListId}", array_filter([
                'returnRecognitionModel' => $returnRecognitionModel,
            ])),
            $this->getClient()
        );
    }

    /**
     * List face lists’ faceListId, name, userData and recognitionModel.
     *
     * @param string $start
     * @param integer $top
     * @param boolean $returnRecognitionModel
     * @return \cognitive_services\FaceList[]
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
            $lists[] = new FaceList($item, $this->getClient());
        }
        return $lists;
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
        if (!$this->faceListId) {
            throw new UninitializedException('"faceListId" is invalid.');
        }

        $this->httpPatch('', array_filter([
            'name' => $name,
            'userData' => $userData,
        ]));
    }
}
