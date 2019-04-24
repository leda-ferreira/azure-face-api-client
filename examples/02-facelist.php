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

require __DIR__ . '/__bootstrap.php';

$faceResource = new Face();
$listResource = new FaceList();

// 1.) FaceList - Create
$listResource->create('facelist-002', 'Library Test 002', 'Test 002', cognitive_services\RECOGNITION_02);

// 2.) FaceList - Get
$list1 = $listResource->get('facelist-002', true);

$image = __DIR__ . '/group.jpg';
$faces = $faceResource->detect($image, true);

// 3.) FaceList - Add Face
$persisted = [];
foreach ($faces as $index => $face) {
    $persisted[] = $list1->addFace($image, "Face Index: {$index}", $face->faceRectangle);
    print "\nAdded face:\n";
    print_r($face);
}

print "\nList of persisted faces:\n";
print_r($persisted);

// 4.) FaceList - Delete Face
foreach ($persisted as $index => $persistedFace) {
    if ($index % 2 === 0) {
        $list1->deleteFace($persistedFace->persistedFaceId);
    }
}

// 5.) FaceList - Update
$list1->update('Updated name 002', 'Updated userData 002');

// 6.) FaceList - List
$lists = $listResource->list();
print_r($lists);

// 7.) FaceList - Delete
$list1->delete();
