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
use cognitive_services\LargeFaceList;

require __DIR__ . '/__bootstrap.php';

$faceResource = new Face();
$listResource = new LargeFaceList();

// 1.) LargeFaceList - Create
$listResource->create('largefacelist-003', 'Library Test 003', 'Test 003', cognitive_services\RECOGNITION_02);

// 2.) LargeFaceList - Get
$list1 = $listResource->get('largefacelist-003', true);

$image = __DIR__ . '/group.jpg';
$faces = $faceResource->detect($image, true);

// 3.) LargeFaceList - Add Face
$persisted = [];
foreach ($faces as $index => $face) {
    $persisted[] = $list1->addFace($image, "Face Index: {$index}", $face->faceRectangle);

    print "\nAdded face:\n";
    print_r($face);
}

print "\nList of persisted faces:\n";
print_r($persisted);

// 4.) LargeFaceList - Delete Face
// 5.) LargeFaceList - Update Face
foreach ($persisted as $index => $persistedFace) {
    if ($index % 2 === 0) {
        $list1->deleteFace($persistedFace->persistedFaceId);
    } else {
        $list1->updateFace($persistedFace->persistedFaceId, "Updated userData: {$index}");
    }
}

// 6.) LargeFaceList - List Face
$faces = $list1->listFace();
print_r($faces);

// 7.) LargeFaceList - Get Face
$face0 = $list1->getFace($faces[0]->persistedFaceId);
print_r($face0);

// 8.) LargeFaceList - Update
$list1->update('Updated name 003', 'Updated userData 003');

// 9.) LargeFaceList - Train
$list1->train();

// 10.) LargeFaceList - Get Training Status
$status = $list1->getTrainingStatus();
print_r($status);

// 11.) LargeFaceList - List
$lists = $listResource->list();
print_r($lists);

// 12.) LargeFaceList - Delete
$list1->delete();
