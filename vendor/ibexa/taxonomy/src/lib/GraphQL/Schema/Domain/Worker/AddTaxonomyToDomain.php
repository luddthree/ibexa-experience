<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\GraphQL\Schema\Domain\Worker;

use Ibexa\GraphQL\Schema\Builder;
use Ibexa\GraphQL\Schema\Builder\Input;

/**
 * @internal
 */
final class AddTaxonomyToDomain extends BaseWorker
{
    /**
     * @param array<string, mixed> $args
     */
    public function work(Builder $schema, array $args): void
    {
        $taxonomy = $args['Taxonomy'];
        $schema->addFieldToType('Domain', new Input\Field(
            $this->taxonomyName($args),
            $this->taxonomyTypeName($args),
            [
                'resolve' => sprintf('@=query("Taxonomy", "%s", args)', $taxonomy),
            ]
        ));
    }

    /**
     * @param array<string, mixed> $args
     */
    public function canWork(Builder $schema, array $args): bool
    {
        return
            isset($args['Taxonomy'])
            && !$schema->hasTypeWithField('Domain', $this->taxonomyName($args));
    }

    /**
     * @param array<string, mixed> $args
     */
    private function taxonomyName(array $args): string
    {
        return $this->getNameHelper()->getTaxonomyName($args['Taxonomy']);
    }

    /**
     * @param array<string, mixed> $args
     */
    private function taxonomyTypeName(array $args): string
    {
        return $this->getNameHelper()->getTaxonomyTypeName($args['Taxonomy']);
    }
}
