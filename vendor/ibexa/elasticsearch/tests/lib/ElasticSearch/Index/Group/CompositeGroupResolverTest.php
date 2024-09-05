<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\ElasticSearch\Index\Group;

use Ibexa\Contracts\Elasticsearch\ElasticSearch\Index\Group\GroupResolverInterface;
use Ibexa\Contracts\Elasticsearch\Mapping\BaseDocument;
use Ibexa\Elasticsearch\ElasticSearch\Index\Group\CompositeGroupResolver;
use PHPUnit\Framework\TestCase;

final class CompositeGroupResolverTest extends TestCase
{
    public function testResolveDocumentGroup(): void
    {
        $resolver = new CompositeGroupResolver([
            $this->createInnerGroupResolver('foo'),
            $this->createInnerGroupResolver('bar'),
        ]);

        $document = $this->createMock(BaseDocument::class);

        self::assertSame(
            'foo_bar',
            $resolver->resolveDocumentGroup($document)
        );
    }

    private function createInnerGroupResolver(string $group): GroupResolverInterface
    {
        $resolver = $this->createMock(GroupResolverInterface::class);
        $resolver->method('resolveDocumentGroup')->willReturn($group);

        return $resolver;
    }
}
