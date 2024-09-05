<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ActivityLog\Query;

use Ibexa\ActivityLog\Query\CriterionMapper;
use Ibexa\Contracts\ActivityLog\CriterionMapperInterface;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\CriterionInterface;
use LogicException;
use PHPUnit\Framework\TestCase;

final class CriterionMapperTest extends TestCase
{
    public function testThrowsExceptionIfCriterionUnhandled(): void
    {
        $criterionMapper = new CriterionMapper([]);

        $criterion = $this->getMockBuilder(CriterionInterface::class)
            ->setMockClassName('CriterionClass')
            ->getMock();

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage(
            'Unable to handle "CriterionClass" criterion. '
            . 'Ensure that a ' . CriterionMapperInterface::class . ' service exists for this '
            . 'criterion and is tagged with ibexa.activity_log.query.criterion_mapper'
        );
        $criterionMapper->handle($criterion);
    }
}
