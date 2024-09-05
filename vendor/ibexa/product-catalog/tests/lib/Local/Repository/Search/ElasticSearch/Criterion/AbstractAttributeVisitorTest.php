<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Repository\Search\ElasticSearch\Criterion;

use Ibexa\Contracts\Elasticsearch\Query\CriterionVisitor;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\ProductCriterionAdapter;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\AbstractAttribute;
use Ibexa\ProductCatalog\Local\Repository\Search\Common\FieldNameBuilder\AttributeFieldNameBuilder;
use Ibexa\ProductCatalog\Local\Repository\Search\ElasticSearch\Criterion\AbstractAttributeVisitor;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\ProductCatalog\Local\Repository\Search\ElasticSearch\Criterion\AbstractAttributeVisitor
 */
final class AbstractAttributeVisitorTest extends TestCase
{
    /**
     * @dataProvider provideForVisit
     *
     * @param scalar|null $value
     * @param array{
     *     type?: string,
     *     criterion_value?: string,
     *     search_identifier?: string
     * } $options
     * @param array<string, mixed> $expectedResult
     */
    public function testVisit($value, array $options, array $expectedResult): void
    {
        $visitor = $this->createVisitor($options);

        $productCriterion = $this->getProductCriterionMock($value);
        $criterion = new ProductCriterionAdapter($productCriterion);

        $result = $visitor->visit(
            $this->createMock(CriterionVisitor::class),
            $criterion,
            new LanguageFilter([], false, false)
        );

        self::assertSame($expectedResult, $result);
    }

    /**
     * @return iterable<string, array{
     *     scalar|null,
     *     array{
     *         type?: string,
     *         criterion_value?: string,
     *         search_identifier?: string
     *     },
     *     array<string, mixed>,
     * }>
     */
    public function provideForVisit(): iterable
    {
        yield 'null as value' => [
            null,
            [],
            [
                'term' => [
                    'product_attribute_<FOO_IDENTIFIER>_is_null_b' => true,
                ],
            ],
        ];

        yield '<FOO_VALUE> as value' => [
            '<FOO_VALUE>',
            [],
            [
                'term' => [
                    'product_attribute_<FOO_IDENTIFIER>_value_<FOO_TYPE>' => '<FOO_VALUE>',
                ],
            ],
        ];

        yield 'Custom search identifier in descendant' => [
            '<FOO_VALUE>',
            [
                'search_identifier' => '<SEARCH_IDENTIFIER>',
            ],
            [
                'term' => [
                    'product_attribute_<SEARCH_IDENTIFIER>_value_<FOO_TYPE>' => '<FOO_VALUE>',
                ],
            ],
        ];

        yield 'Custom value in descendant' => [
            '<FOO_VALUE>',
            [
                'criterion_value' => '<CRITERION_VALUE>',
            ],
            [
                'term' => [
                    'product_attribute_<FOO_IDENTIFIER>_value_<FOO_TYPE>' => '<CRITERION_VALUE>',
                ],
            ],
        ];
    }

    /**
     * @phpstan-param array{
     *     type?: string,
     *     criterion_value?: string,
     *     search_identifier?: string
     * } $options
     *
     * @return \Ibexa\ProductCatalog\Local\Repository\Search\ElasticSearch\Criterion\AbstractAttributeVisitor<
     *     \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\AbstractAttribute<mixed>,
     * >
     */
    private function createVisitor(array $options = []): AbstractAttributeVisitor
    {
        /** @phpstan-ignore-next-line */
        return new class($options) extends AbstractAttributeVisitor {
            /**
             * @phpstan-var array{
             *     type?: string,
             *     criterion_value?: string,
             *     search_identifier?: string
             * }
             */
            private array $options;

            /**
             * @phpstan-param array{
             *     type?: string,
             *     criterion_value?: string,
             *     search_identifier?: string
             * } $options
             */
            public function __construct(array $options)
            {
                $this->options = $options;
            }

            /**
             * @return class-string
             */
            protected function getCriterionClass(): string
            {
                return AbstractAttribute::class;
            }

            /**
             * @param \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\AbstractAttribute<mixed> $criterion
             */
            protected function getAttributeType(AbstractAttribute $criterion): string
            {
                /** @phpstan-ignore-next-line */
                return $this->options['type'] ?? '<FOO_TYPE>';
            }

            /**
             * @param \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\AbstractAttribute<mixed> $criterion
             */
            protected function getCriterionValue(AbstractAttribute $criterion)
            {
                return $this->options['criterion_value'] ?? parent::getCriterionValue($criterion);
            }

            /**
             * @param \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\AbstractAttribute<mixed> $criterion
             */
            protected function getAttributeFieldNameBuilder(AbstractAttribute $criterion): AttributeFieldNameBuilder
            {
                $fieldNameBuilder = parent::getAttributeFieldNameBuilder($criterion);

                if (isset($this->options['search_identifier'])) {
                    $fieldNameBuilder->withIdentifier($this->options['search_identifier']);
                }

                return $fieldNameBuilder;
            }
        };
    }

    /**
     * @param scalar|null $value
     *
     * @return \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\AbstractAttribute<mixed>
     */
    private function getProductCriterionMock($value): AbstractAttribute
    {
        $productCriterion = $this->createMock(AbstractAttribute::class);
        $productCriterion->method('getIdentifier')->willReturn('<FOO_IDENTIFIER>');
        $productCriterion->method('getOperator')->willReturn('=');
        $productCriterion->method('getValue')->willReturn($value);

        return $productCriterion;
    }
}
