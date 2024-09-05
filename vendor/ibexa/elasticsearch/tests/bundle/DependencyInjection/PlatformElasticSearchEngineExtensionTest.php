<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Elasticsearch\DependencyInjection;

use Ibexa\Bundle\Elasticsearch\DependencyInjection\IbexaElasticsearchExtension;
use Ibexa\Contracts\Elasticsearch\ElasticSearch\Index\Group\GroupResolverInterface;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

final class PlatformElasticSearchEngineExtensionTest extends AbstractExtensionTestCase
{
    protected function getContainerExtensions(): array
    {
        return [
            new IbexaElasticsearchExtension(),
        ];
    }

    public function testDefaultConfiguration(): void
    {
        $this->load(/* Empty configuration */);
        $this->compile();

        $definition = $this->container->getAlias(GroupResolverInterface::class);

        self::assertEquals(
            'ibexa.elasticsearch.index.group.default_group_resolver',
            (string)$definition
        );
    }

    public function testCustomConfiguration(): void
    {
        $this->load(['document_group_resolver' => 'app.document_group_resolver']);
        $this->compile();

        $definition = $this->container->getAlias(GroupResolverInterface::class);

        self::assertEquals(
            'app.document_group_resolver',
            (string)$definition
        );
    }
}
