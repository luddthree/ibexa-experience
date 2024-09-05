<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Dashboard\Specification;

use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Dashboard\Specification\IsPredefinedDashboard;
use PHPUnit\Framework\TestCase;
use stdClass;

final class IsPredefinedDashboardTest extends TestCase
{
    /**
     * @covers \Ibexa\Dashboard\Specification\IsPredefinedDashboard::isSatisfiedBy
     */
    public function testIsSatisfiedByInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Argument \'$item\' is invalid: Must be an instance of Ibexa\Contracts\Core\Repository\Values\Content\Location'
        );

        $specification = new IsPredefinedDashboard('containerRemoteId');
        /** @phpstan-ignore-next-line */
        $specification->isSatisfiedBy(new stdClass());
    }

    /**
     * @covers \Ibexa\Dashboard\Specification\IsPredefinedDashboard::isSatisfiedBy
     */
    public function testIsSatisfiedByPredefinedDashboard(): void
    {
        $specification = new IsPredefinedDashboard('containerRemoteId');
        $parent = $this->createLocation('containerRemoteId');

        self::assertTrue($specification->isSatisfiedBy($this->createLocation('foo', $parent)));
    }

    /**
     * @covers \Ibexa\Dashboard\Specification\IsPredefinedDashboard::isSatisfiedBy
     */
    public function testIsSatisfiedByReturnFalse(): void
    {
        $specification = new IsPredefinedDashboard('containerRemoteId');
        $parent = $this->createLocation('bar');

        self::assertFalse($specification->isSatisfiedBy($this->createLocation('foo', $parent)));
    }

    private function createLocation(string $remoteId, ?Location $parent = null): Location
    {
        $location = $this->createMock(Location::class);
        $location
            ->method('__get')
            ->with('remoteId')
            ->willReturn($remoteId);
        $location
            ->method('__isset')
            ->with('remoteId')
            ->willReturn(true);

        if ($parent !== null) {
            $location
                ->method('getParentLocation')
                ->willReturn($parent);
        }

        return $location;
    }
}
