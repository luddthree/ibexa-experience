<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Mapper;

use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntryAssignment;
use Ibexa\Core\Repository\ProxyFactory\ProxyDomainMapperInterface;
use Ibexa\Taxonomy\Persistence\Entity\TaxonomyEntryAssignment as PersistenceEntryAssignment;
use Ibexa\Taxonomy\Proxy\ProxyDomainMapper;

final class EntryAssignmentDomainMapper implements EntryAssignmentDomainMapperInterface
{
    private ProxyDomainMapperInterface $repositoryProxyDomainMapper;

    private ProxyDomainMapper $entryProxyDomainMapper;

    public function __construct(
        ProxyDomainMapperInterface $repositoryProxyDomainMapper,
        ProxyDomainMapper $entryProxyDomainMapper
    ) {
        $this->repositoryProxyDomainMapper = $repositoryProxyDomainMapper;
        $this->entryProxyDomainMapper = $entryProxyDomainMapper;
    }

    public function buildDomainObjectFromPersistence(
        PersistenceEntryAssignment $entryAssignment
    ): TaxonomyEntryAssignment {
        $content = $this->repositoryProxyDomainMapper->createContentProxy($entryAssignment->getContent());
        $entry = $this->entryProxyDomainMapper->createEntryProxy($entryAssignment->getEntry()->getId());

        return new TaxonomyEntryAssignment(
            $entryAssignment->getId(),
            $content,
            $entry
        );
    }
}
