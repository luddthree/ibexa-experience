<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\Query\CriterionVisitor\Iterator;

use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator;
use Ibexa\Contracts\Core\Search\Field as SearchField;
use Ibexa\Contracts\Core\Search\FieldType\StringField;
use Ibexa\Core\Search\Common\FieldNameResolver;
use Ibexa\Core\Search\Common\FieldValueMapper;
use Ibexa\Elasticsearch\Query\CriterionVisitor\Iterator\FieldCriterionTargetIterator;
use Ibexa\Tests\Elasticsearch\Query\Utils\Stub\TestCriterion;
use PHPUnit\Framework\TestCase;

final class FieldCriterionTargetIteratorTest extends TestCase
{
    /** @var \Ibexa\Core\Search\Common\FieldNameResolver|\PHPUnit\Framework\MockObject\MockObject */
    private $fieldNameResolver;

    /** @var \Ibexa\Core\Search\Common\FieldValueMapper|\PHPUnit\Framework\MockObject\MockObject */
    private $fieldValueMapper;

    protected function setUp(): void
    {
        $this->fieldNameResolver = $this->createMock(FieldNameResolver::class);
        $this->fieldValueMapper = $this->createMock(FieldValueMapper::class);
    }

    public function testGetIterator(): void
    {
        $criterion = new TestCriterion('field', Operator::IN, ['value', null]);

        $this->fieldNameResolver
            ->method('getFieldTypes')
            ->with($criterion, $criterion->target)
            ->willReturn([
                'field_a' => new StringField(),
                'field_b' => new StringField(),
            ]);

        $this->fieldValueMapper
            ->method('map')
            ->willReturnCallback(static function (SearchField $field): ?array {
                if ($field->getValue() !== null) {
                    return ['map(' . $field->getValue() . ')'];
                }

                return null;
            });

        $iterator = new FieldCriterionTargetIterator(
            $this->fieldNameResolver,
            $this->fieldValueMapper,
            $criterion
        );

        $this->assertIterableResults(
            [
                ['field_a', 'map(value)'],
                ['field_a', null],
                ['field_b', 'map(value)'],
                ['field_b', null],
            ],
            $iterator
        );
    }

    public function testGetIteratorThrowsInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $criterion = $this->createMock(Criterion::class);

        $this->fieldNameResolver
            ->method('getFieldTypes')
            ->with($criterion, $criterion->target)
            ->willReturn([]);

        $iterator = new FieldCriterionTargetIterator(
            $this->fieldNameResolver,
            $this->fieldValueMapper,
            $criterion
        );

        foreach ($iterator as $field => $name) {
            /* Exception should be thrown */
        }
    }

    private function assertIterableResults(array $expectedResult, iterable $iter): void
    {
        $actualResults = [];
        foreach ($iter as $key => $value) {
            $actualResults[] = [$key, $value];
        }

        $this->assertEquals($expectedResult, $actualResults);
    }
}

class_alias(FieldCriterionTargetIteratorTest::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\Query\CriterionVisitor\Iterator\FieldCriterionTargetIteratorTest');
