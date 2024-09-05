<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Mapper;

use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Core\Repository\ProxyFactory\ProxyDomainMapperInterface;
use Ibexa\Taxonomy\Persistence\Entity\TaxonomyEntry as PersistenceTaxonomyEntry;

final class EntryDomainMapper implements EntryDomainMapperInterface
{
    private ProxyDomainMapperInterface $repositoryProxyDomainMapper;

    public function __construct(ProxyDomainMapperInterface $repositoryProxyDomainMapper)
    {
        $this->repositoryProxyDomainMapper = $repositoryProxyDomainMapper;
    }

    public function buildDomainObjectFromPersistence(PersistenceTaxonomyEntry $entry, ?TaxonomyEntry $parentEntry): TaxonomyEntry
    {
        $content = $this->repositoryProxyDomainMapper->createContentProxy($entry->getContentId());

        return new TaxonomyEntry(
            $entry->getId(),
            $entry->getIdentifier(),
            $entry->getName(),
            $entry->getMainLanguageCode(),
            $entry->getNames(),
            $parentEntry,
            $content,
            $entry->getTaxonomy(),
            $entry->getLevel(),
            $entry->getContentId(),
        );
    }
}
