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

use cognitive_services\exception\ModelException;
use cognitive_services\exception\UninitializedException;
use cognitive_services\face\FaceAttributes;
use cognitive_services\face\FaceLandmarks;
use cognitive_services\face\FaceRectangle;
use cognitive_services\result\GroupResult;
use cognitive_services\result\IdentifyResult;
use cognitive_services\result\SimilarResult;
use cognitive_services\result\VerifyResult;

/**
 * \cognitive_services\Face.
 *
 * @property-read string $faceId
 * @property-read string $recognitionModel
 * @property-read FaceRectangle $faceRectangle
 * @property-read FaceLandmarks $faceLandmarks
 * @property-read FaceAttributes $faceAttributes
 */
class Face extends ClientModel
{
    /**
     * {@inheritdoc}
     */
    protected $populateClassMap = [
        'faceRectangle' => FaceRectangle::class,
        'faceLandmarks' => FaceLandmarks::class,
        'faceAttributes' => FaceAttributes::class,
    ];

    /**
     * Detect human faces in an image, return face rectangles,
     * and optionally with faceIds, landmarks, and attributes.
     *
     * @param string $url
     * @param boolean $returnFaceId
     * @param boolean $returnFaceLandmarks
     * @param array|string $returnFaceAttributes
     * @param string $recognitionModel
     * @param boolean $returnRecognitionModel
     * @return \cognitive_services\Face[]
     * @throws \InvalidArgumentException
     */
    public function detect(
        $url,
        $returnFaceId = null,
        $returnFaceLandmarks = null,
        $returnFaceAttributes = null,
        $recognitionModel = null,
        $returnRecognitionModel = null
    ) {
        if (is_array($returnFaceAttributes)) {
            $returnFaceAttributes = implode(',', $returnFaceAttributes);
        }

        $endpoint = $this->query('/detect', [
            'returnFaceId' => $returnFaceId,
            'returnFaceLandmarks' => $returnFaceLandmarks,
            'returnFaceAttributes' => $returnFaceAttributes,
            'recognitionModel' => $recognitionModel,
            'returnRecognitionModel' => $returnRecognitionModel,
        ]);

        if (is_remote_url($url)) {
            $result = $this->httpPost($endpoint, ['url' => $url]);
        } elseif (is_local_file($url)) {
            $result = $this->httpStream($endpoint, $url);
        } else {
            throw new ModelException('"url" is not a valid URL or a valid local file.');
        }

        $faces = [];
        foreach ($result as $item) {
            $faces[] = new Face($item, $this->getClient());
        }
        return $faces;
    }

    /**
     * Given query face's faceId, to search the similar-looking faces
     * from a faceId array, a face list or a large face list.
     *
     * @param string $faceListId
     * @param string $largeFaceListId
     * @param array $faceIds
     * @param integer $maxNumOfCandidatesReturned
     * @param string $mode
     * @return SimilarResult[]
     * @throws UninitializedException
     */
    public function findSimilar(
        $faceListId = null,
        $largeFaceListId = null,
        array $faceIds = null,
        $maxNumOfCandidatesReturned = null,
        $mode = null
    ) {
        if (!$this->faceId) {
            throw new UninitializedException('"faceId" is invalid.');
        }

        $result = $this->httpPost('/findsimilars', [
            'faceId' => $this->faceId,
            'faceListId' => $faceListId,
            'largeFaceListId' => $largeFaceListId,
            'faceIds' => $faceIds,
            'maxNumOfCandidatesReturned' => $maxNumOfCandidatesReturned,
            'mode' => $mode,
        ]);

        $similars = [];
        foreach ($result as $item) {
            $similars = new SimilarResult($item);
        }
        return $similars;
    }

    /**
     * Divide candidate faces into groups based on face similarity.
     *
     * @param array $faceIds
     * @return array
     */
    public function group(array $faceIds)
    {
        return new GroupResult(
            $this->httpPost('/group', ['faceIds' => $faceIds])
        );
    }

    /**
     * 1-to-many identification to find the closest matches of the specific
     * query person face from a person group or large person group.
     *
     * @param array $faceIds
     * @param string $personGroupId
     * @param string $largePersonGroupId
     * @param integer $maxNumOfCandidatesReturned
     * @param float $confidenceThreshold
     * @return object[]
     */
    public function identify(
        array $faceIds = null,
        $personGroupId = null,
        $largePersonGroupId = null,
        $maxNumOfCandidatesReturned = null,
        $confidenceThreshold = null
    ) {
        return new IdentifyResult(
            $this->httpPost('/identify', array_filter([
                'faceIds' => $faceIds,
                'personGroupId' => $personGroupId,
                'largePersonGroupId' => $largePersonGroupId,
                'maxNumOfCandidatesReturned' => $maxNumOfCandidatesReturned,
                'confidenceThreshold' => $confidenceThreshold,
            ]))
        );
    }

    /**
     * Verify whether two faces belong to a same person.
     *
     * @param string $faceId
     * @return VerifyResult
     * @throws UninitializedException
     */
    public function verifyFace($faceId)
    {
        if (!$this->faceId) {
            throw new UninitializedException('"faceId" is invalid.');
        }

        return new VerifyResult(
            $this->httpPost('/verify', [
                'faceId1' => $this->faceId,
                'faceId2' => $faceId,
            ])
        );
    }

    /**
     * Verify whether one face belongs to a person.
     *
     * @param string $personId
     * @param string $personGroupId
     * @param string $largePersonGroupId
     * @return VerifyResult
     * @throws UninitializedException
     */
    public function verifyPerson($personId, $personGroupId = null, $largePersonGroupId = null)
    {
        if (!$this->faceId) {
            throw new UninitializedException('"faceId" is invalid.');
        }

        return new VerifyResult(
            $this->httpPost('/verify', array_filter([
                'faceId' => $this->faceId,
                'personGroupId' => $personGroupId,
                'largePersonGroupId' => $largePersonGroupId,
                'personId' => $personId,
            ]))
        );
    }
}
