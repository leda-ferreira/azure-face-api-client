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
use cognitive_services\LargePersonGroup;
use cognitive_services\Person;

require __DIR__ . '/__bootstrap.php';

$faceResource = new Face();
$groupResource = new LargePersonGroup();

// 1.) LargePersonGroup - Create
$groupResource->create('largepersongroup-005', 'Library Test 005', 'Test 005', cognitive_services\RECOGNITION_02);

// 2.) LargePersonGroup - Get
$group1 = $groupResource->get('largepersongroup-005', true);

// add person
$image = __DIR__ . '/group.jpg';
$faces = $faceResource->detect($image, true);

$personResource = new Person(null, null, 'largepersongroup-005');
foreach ($faces as $index => $face) {
    $person = $personResource->create("Person {$index}", "UserData {$index}");
    $person->addFace($image, "Face Index: {$index}", $face->faceRectangle);

    print "\nCreated person:\n";
    print_r($person);
}

// 3.) LargePersonGroup - Update
$group1->update('Updated name 005', 'Updated userData 005');

// 4.) LargePersonGroup - Train
$group1->train();

// 5.) LargePersonGroup - Get Training Status
$status = $group1->getTrainingStatus();
print_r($status);

// 6.) LargePersonGroup - List
$groups = $groupResource->list();
print_r($groups);

// 7.) LargePersonGroup - Delete
$group1->delete();

