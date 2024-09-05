<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\ElasticSearch\Index\Group;

use Ibexa\Contracts\Elasticsearch\Mapping\BaseDocument;
use Ibexa\Contracts\Elasticsearch\Mapping\ContentDocument;
use Ibexa\Elasticsearch\ElasticSearch\Index\Group\ContentTypeGroupResolver;
use PHPUnit\Framework\TestCase;

final class ContentTypeGroupResolverTest extends TestCase
{
    private const EXAMPLE_CONTENT_TYPE_ID = 1;

    public function testResolveDocumentGroup(): void
    {
        $document = $this->createExampleDocument(self::EXAMPLE_CONTENT_TYPE_ID);

        $resolver = new ContentTypeGroupResolver();

        self::assertSame(
            '1',
            $resolver->resolveDocumentGroup($document)
        );
    }

    public function testResolveDocumentGroupWithLimit(): void
    {
        $resolver = new ContentTypeGroupResolver(3);

        $groups = [];
        for ($i = 0; $i < 10; ++$i) {
            $groups[] = $resolver->resolveDocumentGroup(
                $this->createExampleDocument(self::EXAMPLE_CONTENT_TYPE_ID + $i)
            );
        }
        self::assertSame(
            ['1', '2', '0', '1', '2', '0', '1', '2', '0', '1'],
            $groups
        );
    }

    private function createExampleDocument(int $contentTypeId): BaseDocument
    {
        $document = new ContentDocument();
        $document->contentTypeId = $contentTypeId;

        return $document;
    }
}
