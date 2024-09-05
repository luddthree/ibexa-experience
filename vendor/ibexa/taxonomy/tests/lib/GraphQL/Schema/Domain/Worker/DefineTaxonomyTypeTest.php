<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Taxonomy\GraphQL\Schema\Domain\Worker;

use Ibexa\GraphQL\Schema\Builder;
use Ibexa\GraphQL\Schema\Builder\SchemaBuilder;
use Ibexa\Taxonomy\GraphQL\Schema\Domain\Worker\BaseWorker;
use Ibexa\Taxonomy\GraphQL\Schema\Domain\Worker\DefineTaxonomyType;

final class DefineTaxonomyTypeTest extends AbstractWorkerTest
{
    protected function getWorker(): BaseWorker
    {
        return new DefineTaxonomyType();
    }

    /**
     * @dataProvider dataProviderForTestCanWork
     *
     * @param array<string, mixed> $args
     */
    public function testCanWork(Builder $schema, array $args, bool $expectedResult): void
    {
        $this->nameHelper
            ->method('getTaxonomyTypeName')
            ->with('foo')
            ->willReturn('TaxonomyFoo');

        self::assertEquals(
            $expectedResult,
            $this->worker->canWork($schema, $args),
        );
    }

    /**
     * @return iterable<array{
     *     \Ibexa\GraphQL\Schema\Builder,
     *     array<string, mixed>,
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
        $schema->addType(new Builder\Input\Type('TaxonomyFoo', 'TaxonomyFoo'));

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
            ->method('getTaxonomyTypeName')
            ->with('foo')
            ->willReturn('TaxonomyFoo');

        $schema = new SchemaBuilder($this->createNameValidatorMock());
        $args = ['Taxonomy' => 'foo'];

        $this->worker->work($schema, $args);

        self::assertTrue(
            $schema->hasType('TaxonomyFoo')
        );

        self::assertEquals(
            [
                'type' => 'object',
                'inherits' => ['BaseTaxonomy'],
                'config' => [
                    'fields' => [
                        'single' => [
                            'type' => 'TaxonomyEntry',
                            'resolve' => '@=query("TaxonomyEntry", args, "foo")',
                            'args' => [
                                'id' => ['type' => 'Int'],
                                'identifier' => ['type' => 'String'],
                                'contentId' => ['type' => 'Int'],
                            ],
                            'description' => 'Fetch single Taxonomy Entry using ID, identifier or contentId',
                        ],
                        'all' => [
                            'type' => 'TaxonomyEntryConnection',
                            'resolve' => '@=query("TaxonomyEntryAll", args, "foo")',
                            'argsBuilder' => 'Relay::Connection',
                            'description' => 'Fetch multiple Taxonomy Entries',
                        ],
                    ],
                ],
            ],
            $schema->getSchema()['TaxonomyFoo'],
        );
    }
}
