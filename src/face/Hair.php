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
 * Group of hair values indicating whether the hair is visible, bald,
 * and hair color if hair is visible.
 *
 * @property-read float $bald
 * @property-read bool $invisible
 * @property-read HairColor[] hairColor
 */
class Hair extends \cognitive_services\Model
{
    /**
     * {@inheritdoc}
     */
    protected $populateListMap = [
        'hairColor' => HairColor::class,
    ];
}
