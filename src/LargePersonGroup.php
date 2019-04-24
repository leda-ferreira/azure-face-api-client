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
 * \cognitive_services\LargePersonGroup.
 *
 * @property-read string $largePersonGroupId
 * @property-read string $name
 * @property-read string $userData
 * @property-read string $recognitionModel
 */
class LargePersonGroup extends ClientModel
{
    /**
     * {@inheritdoc}
     */
    protected function getBaseUrl()
    {
        return '/largepersongroups' . (
            $this->largePersonGroupId ? "/{$this->largePersonGroupId}" : ''
        );
    }

    /**
     * Create a new large person group with specified largePersonGroupId,
     * name, user-provided userData and recognitionModel.
     *
     * @param string $largePersonGroupId
     * @param string $name
     * @param string $userData
     * @param string $recognitionModel
     * @return LargePersonGroup
     */
    public function create($largePersonGroupId, $name, $userData = null, $recognitionModel = null)
    {
        $this->httpPut("/{$largePersonGroupId}", array_filter([
            'name' => $name,
            'userData' => $userData,
            'recognitionModel' => $recognitionModel,
        ]));

        return new LargePersonGroup([
            'largePersonGroupId' => $largePersonGroupId,
            'name' => $name,
            'userData' => $userData,
            'recognitionModel' => $recognitionModel,
        ], $this->getClient());
    }

    /**
     * Delete an existing large person group with specified largePersonGroupId.
     *
     * @return void
     * @throws UninitializedException
     */
    public function delete()
    {
        if (!$this->largePersonGroupId) {
            throw new UninitializedException('"largePersonGroupId" is invalid.');
        }

        $this->httpDelete('');
    }

    /**
     * Retrieve the information of a large person group,
     * including its name, userData and recognitionModel.
     *
     * @param string $largePersonGroupId
     * @param string $returnRecognitionModel
     * @return \cognitive_services\LargePersonGroup
     */
    public function get($largePersonGroupId, $returnRecognitionModel = null)
    {
        return new LargePersonGroup(
            $this->httpGet("/{$largePersonGroupId}", array_filter([
                'returnRecognitionModel' => $returnRecognitionModel,
            ])),
            $this->getClient()
        );
    }

    /**
     * Checks if the large person group's training status is completed or still ongoing.
     *
     * @return TrainingStatus
     * @throws UninitializedException
     */
    public function getTrainingStatus()
    {
        if (!$this->largePersonGroupId) {
            throw new UninitializedException('"largePersonGroupId" is invalid.');
        }

        return new TrainingStatus(
            $this->httpGet("/training")
        );
    }

    /**
     * List all existing large person groups’s largePersonGroupId,
     * name, userData and recognitionModel.
     *
     * @param string $start
     * @param integer $top
     * @param boolean $returnRecognitionModel
     * @return \cognitive_services\LargePersonGroup[]
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
     * Submit a large person group training task.
     *
     * @return void
     * @throws UninitializedException
     */
    public function train()
    {
        if (!$this->largePersonGroupId) {
            throw new UninitializedException('"largePersonGroupId" is invalid.');
        }

        $this->httpPost('/train');
    }

    /**
     * Update an existing large person group's name and userData.
     *
     * @param string $name
     * @param string $userData
     * @return void
     * @throws UninitializedException
     */
    public function update($name, $userData)
    {
        if (!$this->largePersonGroupId) {
            throw new UninitializedException('"largePersonGroupId" is invalid.');
        }

        $this->httpPatch('', array_filter([
            'name' => $name,
            'userData' => $userData,
        ]));
    }

    /**
     * Returns a new LargePersonGroup Person resource
     * attached to this large person group.
     *
     * @return \cognitive_services\Person
     * @throws UninitializedException
     */
    public function person()
    {
        if (!$this->largePersonGroupId) {
            throw new UninitializedException('"largePersonGroupId" is invalid.');
        }

        return new Person(
            null,
            null,
            $this->largePersonGroupId,
            $this->getClient()
        );
    }
}
