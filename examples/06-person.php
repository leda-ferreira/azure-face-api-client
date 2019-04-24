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
use cognitive_services\PersonGroup;

require __DIR__ . '/__bootstrap.php';

$group = (new PersonGroup())->create('persongroup-006', 'Library Test 006', 'Test 006', cognitive_services\RECOGNITION_02);
$personResource = $group->person();

// 1.) Person - Create
$person = $personResource->create('Person 006', 'UserData 006');

// 2.) Person - Update
$person->update('Updated name 006', 'Updated userData 006');

// 3.) Person - List
$persons = $personResource->list();
print_r($persons);

// 4.) Person - Get
$person0 = $personResource->get($persons[0]->personId);
print_r($person0);

$image = __DIR__ . '/group.jpg';
$faces = (new Face())->detect($image, true);

// 5.) Person - Add Face
$persisted = $person0->addFace($image, 'Face Index: 0', $faces[0]->faceRectangle);
print_r($persisted);

// 6.) Person - Update Face
$person0->updateFace($persisted->persistedFaceId, 'Updated userData');

// 7.) Person - Get Face
$face0 = $person0->getFace($persisted->persistedFaceId);
print_r($face0);

// 8.) Person - Delete Face
$person0->deleteFace($persisted->persistedFaceId);

// 9.) Person - Delete
$person0->delete();
$group->delete();
