<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Elasticsearch\ElasticSearch\Index;

use Ibexa\Bundle\Elasticsearch\ElasticSearch\Index\RepositoryAwareIndexResolver;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\Elasticsearch\Mapping\BaseDocument;
use Ibexa\Elasticsearch\ElasticSearch\Index\IndexResolverInterface;
use PHPUnit\Framework\TestCase;

final class RepositoryAwareIndexResolverTest extends TestCase
{
    /** @var \Ibexa\Elasticsearch\ElasticSearch\Index\IndexResolverInterface */
    private $innerIndexResolver;

    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface */
    private $configResolver;

    /** @var \Ibexa\Bundle\Elasticsearch\ElasticSearch\Index\RepositoryAwareIndexResolver */
    private $indexResolver;

    protected function setUp(): void
    {
        $this->innerIndexResolver = $this->createMock(IndexResolverInterface::class);
        $this->configResolver = $this->createMock(ConfigResolverInterface::class);
        $this->indexResolver = new RepositoryAwareIndexResolver(
            $this->innerIndexResolver,
            $this->configResolver
        );
    }

    public function testGetIndexWildcard(): void
    {
        $this->innerIndexResolver
            ->method('getIndexWildcard')
            ->with(BaseDocument::class)
            ->willReturn('document_*');

        $this->configResolver
            ->method('getParameter')
            ->with('repository', null, null)
            ->willReturn('intranet');

        $this->assertEquals(
            'intranet_document_*',
            $this->indexResolver->getIndexWildcard(BaseDocument::class)
        );
    }

    public function testGetIndexNameForDocument(): void
    {
        $document = $this->createMock(BaseDocument::class);

        $this->innerIndexResolver
            ->method('getIndexNameForDocument')
            ->with($document)
            ->willReturn('document_2');

        $this->configResolver
            ->method('getParameter')
            ->with('repository', null, null)
            ->willReturn('intranet');

        $this->assertEquals(
            'intranet_document_2',
            $this->indexResolver->getIndexNameForDocument($document)
        );
    }
}

class_alias(RepositoryAwareIndexResolverTest::class, 'Ibexa\Platform\Bundle\ElasticSearchEngine\Tests\ElasticSearch\Index\RepositoryAwareIndexResolverTest');
