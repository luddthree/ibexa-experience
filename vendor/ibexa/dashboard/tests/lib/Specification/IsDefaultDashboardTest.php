<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Dashboard\Specification;

use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Specification\SpecificationInterface;
use Ibexa\Dashboard\Specification\IsDefaultDashboard;
use Ibexa\Tests\Dashboard\PhpUnit\ConfigResolverMockTrait;
use Ibexa\Tests\Dashboard\PhpUnit\ContentItemOfContentTypeMockTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Dashboard\Specification\IsDefaultDashboard
 */
final class IsDefaultDashboardTest extends TestCase
{
    use ContentItemOfContentTypeMockTrait;
    use ConfigResolverMockTrait;

    private const PREDEFINED_DASHBOARD_REMOTE_ID = 'default_dashboard';

    private SpecificationInterface $specification;

    protected function setUp(): void
    {
        $this->specification = new IsDefaultDashboard($this->mockConfigResolverWithMap(
            [
                ['dashboard.default_dashboard_remote_id', null, null, self::PREDEFINED_DASHBOARD_REMOTE_ID],
            ]
        ));
    }

    /**
     * @return iterable<string, array{object, bool}>
     */
    public function getDataForTestIsSatisfiedBy(): iterable
    {
        yield 'predefined' => [
            $this->mockLocationOfPredefinedDashboardType(),
            true,
        ];

        yield 'not predefined' => [
            $this->createMock(Location::class),
            false,
        ];
    }

    /**
     * @dataProvider getDataForTestIsSatisfiedBy
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\Exception
     */
    public function testIsSatisfiedBy(Location $item, bool $expectedIsSatisfiedBy): void
    {
        self::assertSame($expectedIsSatisfiedBy, $this->specification->isSatisfiedBy($item));
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\Exception
     */
    public function testIsSatisfiedByThrowsInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->specification->isSatisfiedBy(123);
    }
}
