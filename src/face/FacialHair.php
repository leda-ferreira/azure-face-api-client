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
 * Return lengths in three facial hair areas: moustache, beard and sideburns.
 * The length is a number between [0,1]. 0 for no facial hair in this area,
 * 1 for long or very thick facial hairs in this area.
 *
 * @property-read float $moustache
 * @property-read float $beard
 * @property-read float $sideburns
 */
class FacialHair extends \cognitive_services\Model
{
    // pass
}
