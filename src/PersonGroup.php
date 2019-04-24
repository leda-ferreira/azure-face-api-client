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

use cognitive_services\common\TrainingStatus;
use cognitive_services\exception\UninitializedException;

/**
 * \cognitive_services\PersonGroup.
 *
 * @property-read string $personGroupId
 * @property-read string $name
 * @property-read string $userData
 * @property-read string $recognitionModel
 */
class PersonGroup extends ClientModel
{
    /**
     * {@inheritdoc}
     */
    protected function getBaseUrl()
    {
        return '/persongroups' . (
            $this->personGroupId ? "/{$this->personGroupId}" : ''
        );
    }

    /**
     * Create a new person group with specified personGroupId,
     * name, user-provided userData and recognitionModel.
     *
     * @param string $personGroupId
     * @param string $name
     * @param string $userData
     * @param string $recognitionModel
     * @return PersonGroup
     */
    public function create($personGroupId, $name, $userData = null, $recognitionModel = null)
    {
        $this->httpPut("/{$personGroupId}", array_filter([
            'name' => $name,
            'userData' => $userData,
            'recognitionModel' => $recognitionModel,
        ]));

        return new PersonGroup([
            'personGroupId' => $personGroupId,
            'name' => $name,
            'userData' => $userData,
            'recognitionModel' => $recognitionModel,
        ], $this->getClient());
    }

    /**
     * Delete an existing person group with specified personGroupId.
     *
     * @return void
     * @throws UninitializedException
     */
    public function delete()
    {
        if (!$this->personGroupId) {
            throw new UninitializedException('"personGroupId" is invalid.');
        }

        $this->httpDelete('');
    }

    /**
     * Retrieve person group name, userData and recognitionModel.
     *
     * @param string $personGroupId
     * @param string $returnRecognitionModel
     * @return \cognitive_services\PersonGroup
     */
    public function get($personGroupId, $returnRecognitionModel = null)
    {
        return new PersonGroup(
            $this->httpGet("/{$personGroupId}", array_filter([
                'returnRecognitionModel' => $returnRecognitionModel,
            ])),
            $this->getClient()
        );
    }

    /**
     * Checks if the person group's training status is completed or still ongoing.
     *
     * @return TrainingStatus
     * @throws UninitializedException
     */
    public function getTrainingStatus()
    {
        if (!$this->personGroupId) {
            throw new UninitializedException('"personGroupId" is invalid.');
        }

        return new TrainingStatus(
            $this->httpGet("/training")
        );
    }

    /**
     * List person groups’s personGroupId, name, userData and recognitionModel.
     *
     * @param string $start
     * @param integer $top
     * @param boolean $returnRecognitionModel
     * @return \cognitive_services\PersonGroup[]
     */
    public function list($start = null, $top = null, $returnRecognitionModel = null)
    {
        $result = $this->httpGet('', array_filter([
            'start' => $start,
            'top' => $top,
            'returnRecognitionModel' => $returnRecognitionModel,
        ]));

        $groups = [];
        foreach ($result as $item) {
            $groups[] = new PersonGroup($item, $this->getClient());
        }
        return $groups;
    }

    /**
     * Submit a person group training task.
     *
     * @return void
     * @throws UninitializedException
     */
    public function train()
    {
        if (!$this->personGroupId) {
            throw new UninitializedException('"personGroupId" is invalid.');
        }

        $this->httpPost('/train');
    }

    /**
     * Update an existing person group's name and userData.
     *
     * @param string $name
     * @param string $userData
     * @return void
     * @throws UninitializedException
     */
    public function update($name, $userData)
    {
        if (!$this->personGroupId) {
            throw new UninitializedException('"personGroupId" is invalid.');
        }

        $this->httpPatch('', array_filter([
            'name' => $name,
            'userData' => $userData,
        ]));
    }

    /**
     * Returns a new PersonGroup Person resource attached to this person group.
     *
     * @return \cognitive_services\Person
     * @throws UninitializedException
     */
    public function person()
    {
        if (!$this->personGroupId) {
            throw new UninitializedException('"personGroupId" is invalid.');
        }

        return new Person(
            null,
            $this->personGroupId,
            null,
            $this->getClient()
        );
    }
}
