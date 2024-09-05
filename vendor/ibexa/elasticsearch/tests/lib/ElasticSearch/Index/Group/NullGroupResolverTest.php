<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\ElasticSearch\Index\Group;

use Ibexa\Contracts\Elasticsearch\Mapping\ContentDocument;
use Ibexa\Elasticsearch\ElasticSearch\Index\Group\NullGroupResolver;
use PHPUnit\Framework\TestCase;

final class NullGroupResolverTest extends TestCase
{
    public function testResolveDocumentGroup(): void
    {
        $document = new ContentDocument();

        $resolver = new NullGroupResolver();

        self::assertSame(
            '',
            $resolver->resolveDocumentGroup($document)
        );
    }
}
