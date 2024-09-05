<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Dashboard\Specification;

use Ibexa\Contracts\Core\Persistence\Content\Location as PersistenceLocation;
use Ibexa\Contracts\Core\Persistence\Content\Location\Handler;
use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Specification\SpecificationInterface;
use Ibexa\Dashboard\Specification\IsInDashboardTreeRoot;
use Ibexa\Tests\Dashboard\PhpUnit\ConfigResolverMockTrait;
use Ibexa\Tests\Dashboard\PhpUnit\ContentItemOfContentTypeMockTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Dashboard\Specification\IsDashboardContentType
 */
final class IsInDashboardTreeRootTest extends TestCase
{
    use ContentItemOfContentTypeMockTrait;
    use ConfigResolverMockTrait;

    private SpecificationInterface $specification;

    protected function setUp(): void
    {
        $locationHandler = $this->mockLocationHandler();

        $this->specification = new IsInDashboardTreeRoot(
            $this->mockConfigResolverWithMap(
                [
                    ['dashboard.container_remote_id', null, null, 'dashboard_container'],
                ]
            ),
            $locationHandler
        );
    }

    /**
     * @return iterable<string, array{\Ibexa\Core\Repository\Values\Content\Location, bool}>
     */
    public function getDataForTestIsSatisfiedBy(): iterable
    {
        yield 'dashboard location' => [
            $this->mockLocationOfContentItemOfDashboardType(),
            true,
        ];

        yield 'location outside Dashboard tree root' => [
            $this->mockLocationOfContentItemOfContentType(
                'foo',
                null,
                '/1/2/'
            ),
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

    private function mockLocationHandler(): Handler
    {
        $dashboardContainerLocation = $this->getPersistenceLocation('/1/56/');

        $locationHandler = $this->createMock(PersistenceLocation\Handler::class);
        $locationHandler
            ->method('loadByRemoteId')
            ->willReturn($dashboardContainerLocation);

        return $locationHandler;
    }

    private function getPersistenceLocation(string $pathString): PersistenceLocation
    {
        return new PersistenceLocation(['pathString' => $pathString]);
    }
}
