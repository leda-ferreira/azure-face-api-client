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

use cognitive_services\Face;
use cognitive_services\Person;
use cognitive_services\PersonGroup;

require __DIR__ . '/__bootstrap.php';

$faceResource = new Face();
$groupResource = new PersonGroup();

// 1.) PersonGroup - Create
$groupResource->create('persongroup-004', 'Library Test 004', 'Test 004', cognitive_services\RECOGNITION_02);

// 2.) PersonGroup - Get
$group1 = $groupResource->get('persongroup-004', true);

// add person
$image = __DIR__ . '/group.jpg';
$faces = $faceResource->detect($image, true);

$personResource = new Person(null, 'persongroup-004');
foreach ($faces as $index => $face) {
    $person = $personResource->create("Person {$index}", "UserData {$index}");
    $person->addFace($image, "Face Index: {$index}", $face->faceRectangle);

    print "\nCreated person:\n";
    print_r($person);
}

// 3.) PersonGroup - Update
$group1->update('Updated name 004', 'Updated userData 004');

// 4.) PersonGroup - Train
$group1->train();

// 5.) PersonGroup - Get Training Status
$status = $group1->getTrainingStatus();
print_r($status);

// 6.) PersonGroup - List
$groups = $groupResource->list();
print_r($groups);

// 7.) PersonGroup - Delete
$group1->delete();
