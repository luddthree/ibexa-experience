<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Repository\Search\Common;

use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;
use Ibexa\ProductCatalog\Local\Repository\Search\Common\CriterionConverterRegistry;
use PHPUnit\Framework\TestCase;

final class CriterionConverterRegistryTest extends TestCase
{
    private DummyConverter $converter;

    private CriterionConverterRegistry $registry;

    protected function setUp(): void
    {
        $this->converter = new DummyConverter($this->createMock(Criterion::class));
        $this->registry = new CriterionConverterRegistry([
            DummyCriterion::class => $this->converter,
        ]);
    }

    public function testHasConverter(): void
    {
        $invalid = new class() implements CriterionInterface {
            /* empty implementation */
        };

        self::assertTrue($this->registry->hasConverter(new DummyCriterion()));
        self::assertFalse($this->registry->hasConverter($invalid));
    }

    public function testGetConverter(): void
    {
        self::assertSame($this->converter, $this->registry->getConverter(new DummyCriterion()));
    }

    public function testGetConverterThrowsInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Could not find converter for');

        $invalid = new class() implements CriterionInterface {
            /* empty implementation */
        };

        $this->registry->getConverter($invalid);
    }
}
