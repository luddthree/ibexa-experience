<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\Service\Mapper;

use Ibexa\Segmentation\Value\Persistence\Segment as PersistenceSegment;
use Ibexa\Segmentation\Value\Persistence\SegmentGroup as PersistenceSegmentGroup;
use Ibexa\Segmentation\Value\Segment;
use Ibexa\Segmentation\Value\SegmentGroup;

class DomainMapper
{
    public function createSegmentFromPersistenceObject(
        PersistenceSegment $persistenceSegment,
        PersistenceSegmentGroup $segmentGroup
    ): Segment {
        return new Segment([
            'id' => $persistenceSegment->id,
            'identifier' => $persistenceSegment->identifier,
            'name' => $persistenceSegment->name,
            'group' => $this->createSegmentGroupFromPersistenceObject($segmentGroup),
        ]);
    }

    public function createPersistenceObjectFromSegment(
        Segment $segment
    ): PersistenceSegment {
        return new PersistenceSegment([
            'id' => $segment->id,
            'identifier' => $segment->identifier,
            'name' => $segment->name,
            'groupId' => $segment->group->id,
        ]);
    }

    public function createSegmentGroupFromPersistenceObject(PersistenceSegmentGroup $persistenceSegmentGroup): SegmentGroup
    {
        return new SegmentGroup([
            'id' => $persistenceSegmentGroup->id,
            'identifier' => $persistenceSegmentGroup->identifier,
            'name' => $persistenceSegmentGroup->name,
        ]);
    }

    public function createPersistenceObjectFromSegmentGroup(SegmentGroup $segmentGroup): PersistenceSegmentGroup
    {
        return new PersistenceSegmentGroup([
            'id' => $segmentGroup->id,
            'identifier' => $segmentGroup->identifier,
            'name' => $segmentGroup->name,
        ]);
    }
}

class_alias(DomainMapper::class, 'Ibexa\Platform\Segmentation\Service\Mapper\DomainMapper');
