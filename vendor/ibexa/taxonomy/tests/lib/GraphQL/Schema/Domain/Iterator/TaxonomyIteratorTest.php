<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Taxonomy\GraphQL\Schema\Domain\Iterator;

use Ibexa\GraphQL\Schema\Builder;
use Ibexa\Taxonomy\GraphQL\Schema\Domain\Iterator\TaxonomyIterator;
use Ibexa\Taxonomy\Service\TaxonomyConfiguration;
use PHPUnit\Framework\TestCase;

final class TaxonomyIteratorTest extends TestCase
{
    private TaxonomyIterator $iterator;

    /** @var \Ibexa\Taxonomy\Service\TaxonomyConfiguration|\PHPUnit\Framework\MockObject\MockObject */
    private TaxonomyConfiguration $taxonomyConfiguration;

    protected function setUp(): void
    {
        $this->taxonomyConfiguration = $this->createMock(TaxonomyConfiguration::class);

        $this->iterator = new TaxonomyIterator(
            $this->taxonomyConfiguration,
        );
    }

    public function testInit(): void
    {
        $this->taxonomyConfiguration
            ->expects(self::once())
            ->method('getTaxonomies')
            ->willReturn(['foo', 'bar']);

        $this->iterator->init($this->createMock(Builder::class));
    }

    public function testIterate(): void
    {
        $this->taxonomyConfiguration
            ->expects(self::once())
            ->method('getTaxonomies')
            ->willReturn(['foo', 'bar']);

        // initialize first to fetch taxonomies
        $this->iterator->init($this->createMock(Builder::class));

        self::assertEquals(
            [
                ['Taxonomy' => 'foo'],
                ['Taxonomy' => 'bar'],
            ],
            iterator_to_array($this->iterator->iterate()),
        );
    }
}
