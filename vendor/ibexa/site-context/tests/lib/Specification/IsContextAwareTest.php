<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\SiteContext\Specification;

use Ibexa\Contracts\Core\Exception\InvalidArgumentType;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\SiteContext\Specification\IsContextAware;
use PHPUnit\Framework\TestCase;

final class IsContextAwareTest extends TestCase
{
    public function testIsSatisfiedByReturnsTrueWhenExcludedPathsIsNull(): void
    {
        $this->assertTrue((new IsContextAware(null))->isSatisfiedBy($this->createMock(Location::class)));
    }

    public function testIsSatisfiedByReturnsTrueWhenExcludedPathsIsEmpty(): void
    {
        $this->assertTrue((new IsContextAware([]))->isSatisfiedBy($this->createMock(Location::class)));
    }

    public function testIsSatisfiedByReturnsTrueWhenPathDoesNotMatchExcludedPaths(): void
    {
        $location = $this->createMock(Location::class);
        $location->method('__get')->with('pathString')->willReturn('/1/2');

        $specification = new IsContextAware(['/1/42']);

        $this->assertTrue($specification->isSatisfiedBy($location));
    }

    public function testIsSatisfiedByReturnsFalseWhenPathMatchesExcludedPaths(): void
    {
        $location = $this->createMock(Location::class);
        $location->method('__get')->with('pathString')->willReturn('/1/2/43');

        $specification = new IsContextAware(['/1/2']);

        $this->assertFalse($specification->isSatisfiedBy($location));
    }

    public function testIsSatisfiedByThrowsExceptionForInvalidArgumentType(): void
    {
        $this->expectException(InvalidArgumentType::class);

        (new IsContextAware(null))->isSatisfiedBy('InvalidArgument');
    }

    public function testFromConfigurationReturnsInstanceWithExcludedPathsFromConfigResolver(): void
    {
        $configResolver = $this->createMock(ConfigResolverInterface::class);
        $configResolver->method('getParameter')->with('site_context.excluded_paths')->willReturn(['/config/excluded']);

        $this->assertEquals(
            ['/config/excluded'],
            IsContextAware::fromConfiguration($configResolver)->getExcludedPaths()
        );
    }
}
