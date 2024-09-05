<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Tree;

use Ibexa\Taxonomy\Persistence\Entity\TaxonomyEntry;

/**
 * @internal
 */
final class TreeNodeMapper
{
    /**
     * @return array<string, mixed>
     */
    public function mapToArray(TaxonomyEntry $entry): array
    {
        return [
            'id' => $entry->getId(),
            'identifier' => $entry->getIdentifier(),
            'name' => $entry->getName(),
            'names' => $entry->getNames(),
            'mainLanguageCode' => $entry->getMainLanguageCode(),
            'contentId' => $entry->getContentId(),
            'left' => $entry->getLeft(),
            'right' => $entry->getRight(),
            'level' => $entry->getLevel(),
            'taxonomy' => $entry->getTaxonomy(),
            '__children' => [],
        ];
    }
}
