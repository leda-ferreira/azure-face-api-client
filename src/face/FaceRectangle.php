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
 * A rectangle area for the face location on image.
 *
 * @property-read int $width
 * @property-read int $height
 * @property-read int $left
 * @property-read int $top
 */
class FaceRectangle extends \cognitive_services\Model
{
    /**
     * Converts the face rectangle's values to url parameter format.
     *
     * @return string
     */
    public function asParam()
    {
        return implode(',', [
            $this->left,
            $this->top,
            $this->width,
            $this->height,
        ]);
    }
}
