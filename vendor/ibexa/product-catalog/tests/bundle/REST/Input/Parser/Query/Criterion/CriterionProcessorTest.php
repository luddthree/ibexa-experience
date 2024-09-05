<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\REST\Input\Parser\Query\Criterion;

use Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query\Criterion\CriterionProcessor;
use Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query\Criterion\FloatAttributeRange as FloatAttributeRangeParser;
use Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query\Criterion\ProductAvailability as ProductAvailabilityParser;
use Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query\Criterion\ProductCategory as ProductCategoryParser;
use Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query\Criterion\ProductCode as ProductCodeParser;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\FloatAttributeRange;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductAvailability;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductCategory;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductCode;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\SortClause\ProductCode as ProductCodeSortClause;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\SortClause\ProductName as ProductNameSortClause;
use Ibexa\Contracts\Rest\Exceptions\Parser as ParserException;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Rest\Server\Input\Parser\SortClause\DataKeyValueObjectClass;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @template CR of object
 * @template SC of object
 */
final class CriterionProcessorTest extends TestCase
{
    /** @var \Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query\Criterion\CriterionProcessor<CR,SC> */
    private CriterionProcessor $criterionProcessor;

    protected function setUp(): void
    {
        $this->criterionProcessor = new CriterionProcessor();
    }

    /**
     * @dataProvider provideForTestProcessCorrectCriteriaArray
     *
     * @param array<string, mixed> $inputCriteria
     * @param array<mixed> $expectedOutput
     */
    public function testProcessCorrectCriteriaArray(array $inputCriteria, array $expectedOutput): void
    {
        self::assertEquals(
            $expectedOutput,
            $this->criterionProcessor->processCriteriaArray(
                $inputCriteria,
                $this->getParsingDispatcher()
            )
        );
    }

    /**
     * @phpstan-return iterable<
     *     string,
     *     array{
     *         array<string, mixed>,
     *         array<mixed>,
     *     },
     * >
     */
    public function provideForTestProcessCorrectCriteriaArray(): iterable
    {
        yield 'Input containing properly formatted criteria' => [
            [
                'ProductCodeCriterion' => 'test',
                'ProductAvailabilityCriterion' => true,
                'ProductCategoryCriterion' => [1, 2],
                'FloatAttributeRangeCriterion' => [
                    'identifier' => 'test',
                    'min' => 20.1,
                    'max' => 100.5,
                ],
            ],
            [
                new ProductCode(['test']),
                new ProductAvailability(true),
                new ProductCategory([1, 2]),
                new FloatAttributeRange('test', 20.1, 100.5),
            ],
        ];
    }

    /**
     * @dataProvider provideForTestProcessIncorrectCriteriaArray
     *
     * @param array<string, mixed> $inputCriteria
     */
    public function testProcessIncorrectCriteriaArray(array $inputCriteria, string $exceptionMessage): void
    {
        $this->expectException(ParserException::class);
        $this->expectExceptionMessage($exceptionMessage);

        $this->criterionProcessor->processCriteriaArray(
            $inputCriteria,
            $this->getParsingDispatcher()
        );
    }

    /**
     * @phpstan-return iterable<
     *     string,
     *     array{
     *         array<string, string>,
     *         string,
     *     },
     * >
     */
    public function provideForTestProcessIncorrectCriteriaArray(): iterable
    {
        yield 'Input containing non-existent criteria' => [
            [
                'Foo' => 'test',
            ],
            'Invalid Criterion id <Foo> in <AND>',
        ];

        yield 'Input containing wrongly formatted criteria' => [
            [
                'ProductCode' => 'test',
            ],
            'Invalid Criterion id <ProductCode> in <AND>',
        ];
    }

    /**
     * @dataProvider provideForTestProcessSortClauses
     *
     * @param array<string, mixed> $inputClauses
     * @param array<\Ibexa\Contracts\ProductCatalog\Values\Common\Query\AbstractSortClause> $expectedOutput
     */
    public function testProcessSortClauses(array $inputClauses, array $expectedOutput): void
    {
        self::assertEquals(
            $expectedOutput,
            $this->criterionProcessor->processSortClauses(
                $inputClauses,
                $this->getParsingDispatcher()
            )
        );
    }

    /**
     * @phpstan-return iterable<
     *     string,
     *     array{
     *         array<string, string>,
     *         array<\Ibexa\Contracts\ProductCatalog\Values\Common\Query\AbstractSortClause>,
     *     },
     * >
     */
    public function provideForTestProcessSortClauses(): iterable
    {
        yield 'Input containing properly formatted clauses' => [
            [
                'ProductName' => 'ascending',
                'ProductCode' => 'descending',
            ],
            [
                new ProductNameSortClause(ProductQuery::SORT_ASC),
                new ProductCodeSortClause(ProductQuery::SORT_DESC),
            ],
        ];
    }

    private function getParsingDispatcher(): ParsingDispatcher
    {
        return new ParsingDispatcher(
            $this->createMock(EventDispatcherInterface::class),
            [
                'application/vnd.ibexa.api.internal.criterion.ProductCode' => new ProductCodeParser(),
                'application/vnd.ibexa.api.internal.criterion.ProductAvailability' => new ProductAvailabilityParser(),
                'application/vnd.ibexa.api.internal.criterion.ProductCategory' => new ProductCategoryParser(),
                'application/vnd.ibexa.api.internal.criterion.FloatAttributeRange' => new FloatAttributeRangeParser(),
                'application/vnd.ibexa.api.internal.sortclause.ProductName' => new DataKeyValueObjectClass(
                    'ProductName',
                    ProductNameSortClause::class
                ),
                'application/vnd.ibexa.api.internal.sortclause.ProductCode' => new DataKeyValueObjectClass(
                    'ProductCode',
                    ProductCodeSortClause::class
                ),
            ]
        );
    }
}
