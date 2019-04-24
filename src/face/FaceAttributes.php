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

namespace cognitive_services\face;

/**
 * A collection of facial attributes.
 *
 * @property-read float $age
 * @property-read string $gender
 * @property-read float $smile
 * @property-read FacialHair $facialHair
 * @property-read string $glasses
 * @property-read HeadPose $headPose
 * @property-read Emotion $emotion
 * @property-read Hair $hair
 * @property-read Makeup $makeup
 * @property-read Occlusion $occlusion
 * @property-read Accessory[] $accessories
 * @property-read Blur $blur
 * @property-read Exposure $exposure
 * @property-read Noise $noise
 */
class FaceAttributes extends \cognitive_services\Model
{
    /**
     * {@inheritdoc}
     */
    protected $populateClassMap = [
        'facialHair' => FacialHair::class,
        'headPose' => HeadPose::class,
        'emotion' => Emotion::class,
        'hair' => Hair::class,
        'makeup' => Makeup::class,
        'occlusion' => Occlusion::class,
        'blur' => Blur::class,
        'noise' => Noise::class,
        'exposure' => Exposure::class,
    ];

    /**
     * {@inheritdoc}
     */
    protected $populateListMap = [
        'accessories' => Accessory::class,
    ];
}
