<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Dashboard\Specification;

use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Specification\SpecificationInterface;
use Ibexa\Dashboard\Specification\IsDashboardContentType;
use Ibexa\Tests\Dashboard\PhpUnit\ConfigResolverMockTrait;
use Ibexa\Tests\Dashboard\PhpUnit\ContentItemOfContentTypeMockTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Dashboard\Specification\IsDashboardContentType
 */
final class IsDashboardContentTypeTest extends TestCase
{
    use ContentItemOfContentTypeMockTrait;
    use ConfigResolverMockTrait;

    private SpecificationInterface $specification;

    protected function setUp(): void
    {
        $this->specification = new IsDashboardContentType($this->mockConfigResolver());
    }

    /**
     * @return iterable<string, array{object, bool}>
     */
    public function getDataForTestIsSatisfiedBy(): iterable
    {
        yield 'dashboard type' => [
            $this->mockDashboardContentType(),
            true,
        ];

        yield 'another content type' => [
            $this->mockContentType('foo'),
            false,
        ];
    }

    /**
     * @dataProvider getDataForTestIsSatisfiedBy
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\Exception
     */
    public function testIsSatisfiedBy(ContentType $item, bool $expectedIsSatisfiedBy): void
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
