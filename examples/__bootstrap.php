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

use cognitive_services\Client;

require dirname(__DIR__) . '/vendor/autoload.php';

$API_KEY = '';  // insert your api key here
$API_REG = '';  // insert your api region here

if (!$API_KEY && !$API_REG) {
    die("Please insert your api key and region.\n");
}

Client::init($API_KEY, $API_REG);
