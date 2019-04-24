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
 * \cognitive_services\face\FaceLandmarks.
 * @property FaceLandmark $pupilLeft
 * @property FaceLandmark $pupilRight
 * @property FaceLandmark $noseTip
 * @property FaceLandmark $mouthLeft
 * @property FaceLandmark $mouthRight
 * @property FaceLandmark $eyebrowLeftOuter
 * @property FaceLandmark $eyebrowLeftOuter
 * @property FaceLandmark $eyebrowLeftInner
 * @property FaceLandmark $eyeLeftOuter
 * @property FaceLandmark $eyeLeftTop
 * @property FaceLandmark $eyeLeftBottom
 * @property FaceLandmark $eyeLeftInner
 * @property FaceLandmark $eyebrowRightInner
 * @property FaceLandmark $eyebrowRightOuter
 * @property FaceLandmark $eyeRightInner
 * @property FaceLandmark $eyeRightTop
 * @property FaceLandmark $eyeRightBottom
 * @property FaceLandmark $eyeRightOuter
 * @property FaceLandmark $noseRootLeft
 * @property FaceLandmark $noseRootRight
 * @property FaceLandmark $noseLeftAlarTop
 * @property FaceLandmark $noseRightAlarTop
 * @property FaceLandmark $noseLeftAlarOutTip
 * @property FaceLandmark $noseRightAlarOutTip
 * @property FaceLandmark $upperLipTop
 * @property FaceLandmark $upperLipBottom
 * @property FaceLandmark $underLipTop
 * @property FaceLandmark $underLipBottom
 */
class FaceLandmarks extends \cognitive_services\Model
{
    /**
     * {@inheritdoc}
     */
    protected function prepareAttributes($attributes = array())
    {
        foreach ($attributes as $attribute => $value) {
            $attributes[$attribute] = new FaceLandmark($value);
        }
        return $attributes;
    }
}
