<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Taxonomy\GraphQL\Schema\Domain\Worker;

use Ibexa\GraphQL\Schema\Builder;
use Ibexa\GraphQL\Schema\Builder\SchemaBuilder;
use Ibexa\Taxonomy\GraphQL\Schema\Domain\Worker\AddTaxonomyToDomain;
use Ibexa\Taxonomy\GraphQL\Schema\Domain\Worker\BaseWorker;

final class AddTaxonomyToDomainTest extends AbstractWorkerTest
{
    protected function getWorker(): BaseWorker
    {
        return new AddTaxonomyToDomain();
    }

    /**
     * @dataProvider dataProviderForTestCanWork
     *
     * @param array<string, mixed> $args
     */
    public function testCanWork(Builder $schema, array $args, bool $expectedResult): void
    {
        $this->nameHelper
            ->method('getTaxonomyName')
            ->with('foo')
            ->willReturn('foo');

        self::assertEquals(
            $expectedResult,
            $this->worker->canWork($schema, $args),
        );
    }

    /**
     * @return iterable<array{
     *     \Ibexa\GraphQL\Schema\Builder,
     *     array<string, string>,
     *     bool,
     * }>
     */
    public function dataProviderForTestCanWork(): iterable
    {
        yield 'type not defined yet' => [
            new SchemaBuilder($this->createNameValidatorMock()),
            [
                'Taxonomy' => 'foo',
            ],
            true,
        ];

        $schema = new SchemaBuilder($this->createNameValidatorMock());
        $schema->addType(new Builder\Input\Type('Domain', 'domain'));
        $schema->addFieldToType(
            'Domain',
            new Builder\Input\Field(
                'foo',
                'TaxonomyFoo'
            )
        );

        yield 'type already defined' => [
            $schema,
            [
                'Taxonomy' => 'foo',
            ],
            false,
        ];

        yield 'taxonomy not present in args' => [
            new SchemaBuilder($this->createNameValidatorMock()),
            [],
            false,
        ];
    }

    public function testWork(): void
    {
        $this->nameHelper
            ->method('getTaxonomyName')
            ->with('foo')
            ->willReturn('foo');

        $this->nameHelper
            ->method('getTaxonomyTypeName')
            ->with('foo')
            ->willReturn('TaxonomyFoo');

        $schema = new SchemaBuilder($this->createNameValidatorMock());
        $schema->addType(new Builder\Input\Type('Domain', 'domain'));

        $args = ['Taxonomy' => 'foo'];

        $this->worker->work($schema, $args);

        self::assertTrue(
            $schema->hasTypeWithField('Domain', 'foo')
        );

        self::assertEquals(
            [
                'type' => 'TaxonomyFoo',
                'resolve' => '@=query("Taxonomy", "foo", args)',
            ],
            $schema->getSchema()['Domain']['config']['fields']['foo'],
        );
    }
}
