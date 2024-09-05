<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Elasticsearch\ElasticSearch\Index;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\Elasticsearch\Mapping\BaseDocument;
use Ibexa\Elasticsearch\ElasticSearch\Index\IndexResolverInterface;

final class RepositoryAwareIndexResolver implements IndexResolverInterface
{
    private const DEFAULT_REPOSITORY_PREFIX = 'default_';

    /** @var \Ibexa\Elasticsearch\ElasticSearch\Index\IndexResolverInterface */
    private $innerIndexResolver;

    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface */
    private $configResolver;

    public function __construct(IndexResolverInterface $innerIndexResolver, ConfigResolverInterface $configResolver)
    {
        $this->innerIndexResolver = $innerIndexResolver;
        $this->configResolver = $configResolver;
    }

    public function getIndexWildcard(string $documentClass): string
    {
        return $this->getRepositoryPrefix() . $this->innerIndexResolver->getIndexWildcard($documentClass);
    }

    public function getIndexNameForDocument(BaseDocument $document): string
    {
        return $this->getRepositoryPrefix() . $this->innerIndexResolver->getIndexNameForDocument($document);
    }

    /**
     * Return repository prefix.
     *
     * WARNING: Must be called on-demand and not in constructors to avoid any issues with SiteAccess scope changes.
     *
     * @return string
     */
    private function getRepositoryPrefix(): string
    {
        $repositoryIdentifier = $this->configResolver->getParameter('repository');
        if (!empty($repositoryIdentifier)) {
            return $repositoryIdentifier . '_';
        }

        return self::DEFAULT_REPOSITORY_PREFIX;
    }
}

class_alias(RepositoryAwareIndexResolver::class, 'Ibexa\Platform\Bundle\ElasticSearchEngine\ElasticSearch\Index\RepositoryAwareIndexResolver');
