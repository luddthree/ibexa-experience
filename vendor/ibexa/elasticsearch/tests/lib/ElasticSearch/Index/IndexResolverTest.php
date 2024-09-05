<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\ElasticSearch\Index;

use Ibexa\Contracts\Elasticsearch\ElasticSearch\Index\Group\GroupResolverInterface;
use Ibexa\Contracts\Elasticsearch\Mapping\ContentDocument;
use Ibexa\Contracts\Elasticsearch\Mapping\LocationDocument;
use Ibexa\Elasticsearch\ElasticSearch\Index\IndexResolver;
use PHPUnit\Framework\TestCase;

final class IndexResolverTest extends TestCase
{
    private const EXAMPLE_CONTENT_TYPE_ID = 2;
    private const EXAMPLE_LANGUAGE_CODE = 'eng-GB';

    /** @var \Ibexa\Elasticsearch\ElasticSearch\Index\IndexResolver */
    private $indexResolver;

    /** @var \Ibexa\Contracts\Elasticsearch\ElasticSearch\Index\Group\GroupResolverInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $groupResolver;

    protected function setUp(): void
    {
        $this->groupResolver = $this->createMock(GroupResolverInterface::class);

        $this->indexResolver = new IndexResolver(
            $this->groupResolver,
            [
                ContentDocument::class => 'content',
                LocationDocument::class => 'location',
            ]
        );
    }

    public function testGetIndexWildcard(): void
    {
        $this->assertEquals(
            'content_*',
            $this->indexResolver->getIndexWildcard(ContentDocument::class)
        );

        $this->assertEquals(
            'location_*',
            $this->indexResolver->getIndexWildcard(LocationDocument::class)
        );
    }

    public function testGetIndexNameForDocument(): void
    {
        $document = new LocationDocument();

        $this->groupResolver->method('resolveDocumentGroup')->with($document)->willReturn('foo');

        $this->assertEquals(
            'location_foo',
            $this->indexResolver->getIndexNameForDocument($document)
        );
    }
}

class_alias(IndexResolverTest::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\ElasticSearch\Index\IndexResolverTest');
