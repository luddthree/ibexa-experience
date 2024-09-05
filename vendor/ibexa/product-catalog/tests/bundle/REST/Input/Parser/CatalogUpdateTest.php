<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\REST\Input\Parser;

use Ibexa\Bundle\ProductCatalog\REST\Input\Parser\CatalogUpdate;
use Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query\Criterion\CriterionProcessor;
use Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query\Criterion\ProductCategory as ProductCategoryParser;
use Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query\Criterion\SelectionAttribute as SelectionAttributeParser;
use Ibexa\Bundle\ProductCatalog\REST\Value\CatalogUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductCategory;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\SelectionAttribute;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @template CR of object
 * @template SC of object
 */
final class CatalogUpdateTest extends TestCase
{
    /** @var \Ibexa\Bundle\ProductCatalog\REST\Input\Parser\CatalogUpdate<CR,SC> */
    private CatalogUpdate $parser;

    protected function setUp(): void
    {
        /** @var \Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query\Criterion\CriterionProcessor<CR,SC> $criterionProcessor */
        $criterionProcessor = new CriterionProcessor();

        $this->parser = new CatalogUpdate($criterionProcessor);
    }

    public function testValidInput(): void
    {
        $criteria = new LogicalAnd([
            new ProductCategory([1, 2]),
            new SelectionAttribute('test_selection_attribute', ['#000000', '#FFFFFF']),
        ]);

        $catalogUpdateStruct = new CatalogUpdateStruct(
            'publish',
            'foo_catalog',
            $criteria,
            ['eng-GB' => 'updated catalog'],
            ['eng-GB' => 'updated catalog description'],
        );

        $parserInput = [
            'transition' => 'publish',
            'identifier' => 'foo_catalog',
            'criteria' => [
                'ProductCategoryCriterion' => [1, 2],
                'SelectionAttributeCriterion' => [
                    'identifier' => 'test_selection_attribute',
                    'value' => ['#000000', '#FFFFFF'],
                ],
            ],
            'names' => [
                'eng-GB' => 'updated catalog',
            ],
            'descriptions' => [
                'eng-GB' => 'updated catalog description',
            ],
        ];

        self::assertEquals(
            $catalogUpdateStruct,
            $this->parser->parse(
                $parserInput,
                $this->getParsingDispatcher()
            )
        );
    }

    private function getParsingDispatcher(): ParsingDispatcher
    {
        return new ParsingDispatcher(
            $this->createMock(EventDispatcherInterface::class),
            [
                'application/vnd.ibexa.api.internal.criterion.ProductCategory' => new ProductCategoryParser(),
                'application/vnd.ibexa.api.internal.criterion.SelectionAttribute' => new SelectionAttributeParser(),
            ]
        );
    }
}
