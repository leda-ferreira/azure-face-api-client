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

const MATCH_FACE = 'matchFace';
const MATCH_PERSON = 'matchPerson';

const RECOGNITION_01 = 'recognition_01';
const RECOGNITION_02 = 'recognition_02';

/**
 * Checks if argument is a remote url.
 * @param strings $url
 * @return boolean
 */
function is_remote_url($url) {
    return parse_url($url, PHP_URL_HOST) !== null;
}

/**
 * Checks if argument is a local file.
 * @param string $path
 * @return boolean
 */
function is_local_file($path) {
    return file_exists($path) && is_file($path);
}
