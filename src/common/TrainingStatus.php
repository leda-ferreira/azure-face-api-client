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

namespace cognitive_services\common;

/**
 * Large person group's training status.
 * Training status: notstarted, running, succeeded, failed.
 * If the person group has never been trained before, the status is notstarted.
 * If the training is ongoing, the status is running.
 * Status succeed means this person group is ready for Face - Identify.
 * Status failed is often caused by no person or no persisted face exist in the person group.
 *
 * @property-read string $status
 * @property-read string $createdDateTime
 * @property-read string $lastActionDateTime
 * @property-read string $lastSuccessfulTrainingDateTime
 * @property-read string $message
 */
class TrainingStatus extends \cognitive_services\Model
{
    // pass
}
