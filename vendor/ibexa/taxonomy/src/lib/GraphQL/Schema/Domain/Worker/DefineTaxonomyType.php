<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\GraphQL\Schema\Domain\Worker;

use Ibexa\GraphQL\Schema\Builder;
use Ibexa\GraphQL\Schema\Builder\Input\Type;

/**
 * @internal
 */
final class DefineTaxonomyType extends BaseWorker
{
    /**
     * @param array<string, mixed> $args
     */
    public function work(Builder $schema, array $args): void
    {
        $taxonomyTypeName = $this->taxonomyTypeName($args);
        $schema->addType(new Type(
            $taxonomyTypeName,
            'object',
            ['inherits' => 'BaseTaxonomy'],
        ));

        $this->addFieldSingle($schema, $args['Taxonomy'], $taxonomyTypeName);
        $this->addFieldAll($schema, $args['Taxonomy'], $taxonomyTypeName);
    }

    /**
     * @param array<string, mixed> $args
     */
    public function canWork(Builder $schema, array $args): bool
    {
        return
            isset($args['Taxonomy'])
            && !$schema->hasType($this->taxonomyTypeName($args));
    }

    /**
     * @param array<string, mixed> $args
     */
    private function taxonomyTypeName($args): string
    {
        return $this->getNameHelper()->getTaxonomyTypeName($args['Taxonomy']);
    }

    private function addFieldSingle(Builder $schema, string $taxonomyName, string $taxonomyTypeName): void
    {
        $schema->addFieldToType(
            $taxonomyTypeName,
            new Builder\Input\Field(
                'single',
                'TaxonomyEntry',
                [
                    'description' => 'Fetch single Taxonomy Entry using ID, identifier or contentId',
                    'resolve' => sprintf('@=query("TaxonomyEntry", args, "%s")', $taxonomyName),
                ],
            )
        );
        $schema->addArgToField(
            $taxonomyTypeName,
            'single',
            new Builder\Input\Arg('id', 'Int'),
        );
        $schema->addArgToField(
            $taxonomyTypeName,
            'single',
            new Builder\Input\Arg('identifier', 'String'),
        );
        $schema->addArgToField(
            $taxonomyTypeName,
            'single',
            new Builder\Input\Arg('contentId', 'Int'),
        );
    }

    private function addFieldAll(Builder $schema, string $taxonomyName, string $taxonomyTypeName): void
    {
        $schema->addFieldToType(
            $taxonomyTypeName,
            new Builder\Input\Field(
                'all',
                'TaxonomyEntryConnection',
                [
                    'description' => 'Fetch multiple Taxonomy Entries',
                    'resolve' => sprintf('@=query("TaxonomyEntryAll", args, "%s")', $taxonomyName),
                    'argsBuilder' => 'Relay::Connection',
                ],
            )
        );
    }
}
