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
use cognitive_services\FaceList;
use cognitive_services\PersonGroup;

require __DIR__ . '/__bootstrap.php';

$faceResource = new Face();

// 1.) Face - Detect
$image = __DIR__ . '/group.jpg';
$faces = $faceResource->detect($image, true, null, null, cognitive_services\RECOGNITION_02);
print "\nDetected faces:\n";
print_r($faces);

// setup for 2
$list = (new FaceList())->create('facelist-001', 'Library Test 001', 'Test 001', cognitive_services\RECOGNITION_02);
foreach ($faces as $index => $face) {
    $list->addFace($image, "Face Index: {$index}", $face->faceRectangle);
}

// 2.) Face - Find Similar
$similar = $faces[0]->findSimilar($list->faceListId);
print "\nFind similar:\n";
print_r($similar);

// 3.) Face - Group
$faceIds = array_column($faces, 'faceId');
$grouped = $faceResource->group($faceIds);
print "\nGroup:\n";
print_r($grouped);

// setup for 4, 5 and 6
$group = (new PersonGroup())->create('persongroup-001', 'Library Test 001', 'Test 001', cognitive_services\RECOGNITION_02);
$personResource = $group->person();

$persons = [];
foreach ($faces as $index => $face) {
    $person = $personResource->create("Person {$index}", "UserData {$index}");
    $person->addFace($image, "Face Index: {$index}", $face->faceRectangle);
    $persons[] = $person;
}
$group->train();

do {
    print("\nWaiting for group training to finish...\n");
    sleep(5);

    $status = $group->getTrainingStatus();
    print "\nGroup training status: {$status->status}\n";
} while ($status->status === 'notstarted' || $status->status === 'running');

if ($status->status !== 'succeeded') {
    print("\nGroup training failed, exiting.\n");
} else {
    // 4.) Face - Identify
    $identified = $faceResource->identify($faceIds, $group->personGroupId);
    print "\nIdentify:\n";
    print_r($identified);

    // 5.) Face - Verify Face
    $verify1 = $faces[0]->verifyFace($faces[1]->faceId);
    print "\nVerify Face:\n";
    print_r($verify1);

    // 6.) Face - Verify Person
    $verify2 = $faces[0]->verifyPerson($persons[0]->personId, $group->personGroupId);
    print "\nVerify Person:\n";
    print_r($verify2);
}

$list->delete();
$group->delete();
