<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Dashboard\DependencyInjection\Container;

use Ibexa\Bundle\Dashboard\DependencyInjection\IbexaDashboardExtension;
use Ibexa\Bundle\Dashboard\EventSubscriber\PageBuilder\IbexaNewsBlockSubscriber;
use Ibexa\Core\Persistence\Cache\Identifier\CacheIdentifierGenerator;
use Ibexa\Dashboard\News\CachedFeed;
use Ibexa\Dashboard\News\Feed;
use Ibexa\Dashboard\News\FeedInterface;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\DefinitionDecoratesConstraint;

final class IbexaNewsBlockCacheLayerDICTest extends AbstractExtensionTestCase
{
    protected function getContainerExtensions(): array
    {
        return [new IbexaDashboardExtension()];
    }

    public function testIbexaNewsCacheLayerDependencyInjectionConfiguration(): void
    {
        $this->load();

        $this->assertContainerBuilderHasService(
            'ibexa.dashboard.block.news.cache_identifier_generator',
            CacheIdentifierGenerator::class
        );
        $this->assertContainerBuilderHasParameter('ibexa.dashboard.block.news.cache.tag_prefix', 'ibx-db');
        $this->assertContainerBuilderHasParameter('ibexa.dashboard.ibexa_news.cache.ttl', 86400);
        $this->assertContainerBuilderHasParameter(
            'ibexa.dashboard.block.news.cache.key_patterns',
            ['rss' => 'r-%%s-%%d']
        );

        $this->assertContainerBuilderHasAlias(FeedInterface::class, CachedFeed::class);
        $this->assertContainerBuilderHasService(CachedFeed::class);
        $this->assertContainerBuilderHasService(Feed::class);
        $this->customAssertContainerBuilderServiceDecoration(CachedFeed::class, Feed::class);
        // too early to check at this point if CachedFeed is properly autowired, so let's check for:
        self::assertTrue($this->container->getDefinition(IbexaNewsBlockSubscriber::class)->isAutowired());
    }

    /**
     * Workaround for a bug. Can be replaced with assertContainerBuilderServiceDecoration once we can upgrade to
     * v5 of matthiasnoback/symfony-dependency-injection-test.
     *
     * @see https://github.com/SymfonyTest/SymfonyDependencyInjectionTest/commit/76663d8ed207800e01dd2d0746cb094154287ab0
     */
    protected function customAssertContainerBuilderServiceDecoration(
        string $serviceId,
        string $decoratedServiceId
    ): void {
        self::assertThat($this->container, new DefinitionDecoratesConstraint($serviceId, $decoratedServiceId));
    }
}
