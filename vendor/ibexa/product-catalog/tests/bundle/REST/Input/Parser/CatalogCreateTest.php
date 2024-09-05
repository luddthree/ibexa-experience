<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\REST\Input\Parser;

use Ibexa\Bundle\ProductCatalog\REST\Input\Parser\CatalogCreate;
use Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query\Criterion\CriterionProcessor;
use Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query\Criterion\FloatAttributeRange as FloatAttributeRangeParser;
use Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query\Criterion\ProductAvailability as ProductAvailabilityParser;
use Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query\Criterion\ProductCategory as ProductCategoryParser;
use Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query\Criterion\ProductCode as ProductCodeParser;
use Ibexa\Bundle\ProductCatalog\REST\Value\CatalogCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\FloatAttributeRange;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductAvailability;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductCategory;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductCode;
use Ibexa\Contracts\Rest\Exceptions\Parser as ParserException;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @template CR of object
 * @template SC of object
 */
final class CatalogCreateTest extends TestCase
{
    /** @var \Ibexa\Bundle\ProductCatalog\REST\Input\Parser\CatalogCreate<CR,SC> */
    private CatalogCreate $parser;

    protected function setUp(): void
    {
        /** @var \Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query\Criterion\CriterionProcessor<CR,SC> $criterionProcessor */
        $criterionProcessor = new CriterionProcessor();

        $this->parser = new CatalogCreate($criterionProcessor);
    }

    public function testValidInput(): void
    {
        $criteria = new LogicalAnd([
            new ProductCode(['test_product']),
            new ProductAvailability(true),
            new ProductCategory([1, 2]),
            new FloatAttributeRange('test_float_attribute', 20, 100),
        ]);

        $catalogCreateStruct = new CatalogCreateStruct(
            'test_catalog',
            $criteria,
            ['eng-GB' => 'test catalog'],
            ['eng-GB' => 'test catalog description'],
            'draft'
        );

        $parserInput = [
            'identifier' => 'test_catalog',
            'criteria' => [
                'ProductCodeCriterion' => 'test_product',
                'ProductAvailabilityCriterion' => true,
                'ProductCategoryCriterion' => [1, 2],
                'FloatAttributeRangeCriterion' => [
                    'identifier' => 'test_float_attribute',
                    'min' => 20,
                    'max' => 100,
                ],
            ],
            'names' => [
                'eng-GB' => 'test catalog',
            ],
            'descriptions' => [
                'eng-GB' => 'test catalog description',
            ],
            'status' => 'draft',
        ];

        self::assertEquals(
            $catalogCreateStruct,
            $this->parser->parse(
                $parserInput,
                $this->getParsingDispatcher()
            )
        );
    }

    /**
     * @phpstan-return iterable<
     *     array{
     *         string,
     *         array{
     *             identifier?: string,
     *             criteria?: array<mixed>,
     *             names?: mixed,
     *             descriptions?: array<mixed>,
     *             status?: string,
     *         },
     *     },
     * >
     */
    public function provideForTestInvalidInput(): iterable
    {
        yield [
            'Missing properties (identifier, criteria) for Catalog.',
            [
                'names' => [
                    'eng-GB' => 'test catalog',
                ],
                'descriptions' => [
                    'eng-GB' => 'test catalog description',
                ],
                'status' => 'draft',
            ],
        ];

        yield [
            'Missing properties (names, status) for Catalog.',
            [
                'identifier' => 'test_catalog',
                'criteria' => [
                    'ProductCodeCriterion' => 'test_product',
                    'ProductAvailabilityCriterion' => true,
                ],
            ],
        ];

        yield [
            'The "names" parameter must be an array.',
            [
                'identifier' => 'test_catalog',
                'criteria' => [
                    'ProductCodeCriterion' => 'test_product',
                    'ProductAvailabilityCriterion' => true,
                ],
                'names' => 'foo',
                'status' => 'draft',
            ],
        ];
    }

    /**
     * @dataProvider provideForTestInvalidInput
     *
     * @phpstan-param string $exceptionMessage
     * @phpstan-param array{
     *     array{
     *         ContentCreate?: mixed,
     *         code?: string,
     *         attributes?: mixed,
     *     },
     * } $input
     */
    public function testInvalidInput(string $exceptionMessage, array $input): void
    {
        $this->expectException(ParserException::class);
        $this->expectExceptionMessage($exceptionMessage);

        $this->parser->parse(
            $input,
            $this->createMock(ParsingDispatcher::class)
        );
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
            ]
        );
    }
}
