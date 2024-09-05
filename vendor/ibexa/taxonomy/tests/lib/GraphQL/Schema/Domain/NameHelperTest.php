<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Taxonomy\GraphQL\Schema\Domain;

use Ibexa\GraphQL\Schema\Domain\Pluralizer;
use Ibexa\Taxonomy\GraphQL\Schema\NameHelper;
use PHPUnit\Framework\TestCase;

final class NameHelperTest extends TestCase
{
    private NameHelper $nameHelper;

    private Pluralizer $pluralizer;

    protected function setUp(): void
    {
        $this->pluralizer = $this->createMock(Pluralizer::class);

        $this->nameHelper = new NameHelper(
            $this->pluralizer,
        );
    }

    /**
     * @dataProvider dataProviderForTestGetTaxonomyName
     */
    public function testGetTaxonomyName(string $identifier, string $expectedName): void
    {
        self::assertEquals(
            $expectedName,
            $this->nameHelper->getTaxonomyName($identifier)
        );
    }

    /**
     * @return iterable<array{
     *     string,
     *     string,
     * }>
     */
    public function dataProviderForTestGetTaxonomyName(): iterable
    {
        yield [
            'foo',
            'fooTaxonomy',
        ];

        yield [
            'Foo',
            'fooTaxonomy',
        ];

        yield [
            'Foo_Bar',
            'fooBarTaxonomy',
        ];

        yield [
            'foo_bar',
            'fooBarTaxonomy',
        ];
    }

    /**
     * @dataProvider dataProviderForTestGetTaxonomyTypeName
     */
    public function testGetTaxonomyTypeName(string $identifier, string $expectedName): void
    {
        self::assertEquals(
            $expectedName,
            $this->nameHelper->getTaxonomyTypeName($identifier)
        );
    }

    /**
     * @return iterable<array{
     *     string,
     *     string,
     * }>
     */
    public function dataProviderForTestGetTaxonomyTypeName(): iterable
    {
        yield [
            'foo',
            'TaxonomyFoo',
        ];

        yield [
            'fooBar',
            'TaxonomyFooBar',
        ];
    }
}
