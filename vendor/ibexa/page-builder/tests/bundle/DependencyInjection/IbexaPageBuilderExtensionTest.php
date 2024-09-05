<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\PageBuilder\DependencyInjection;

use Ibexa\Bundle\PageBuilder\DependencyInjection\IbexaPageBuilderExtension;
use Ibexa\PageBuilder\Siteaccess\PageBuilderSiteAccessResolver;
use Ibexa\PageBuilder\Siteaccess\Resolver\PageBuilderListResolverStrategy;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument;

final class IbexaPageBuilderExtensionTest extends AbstractExtensionTestCase
{
    private const SITE_ACCESS_LIST_RESOLVER_STRATEGY_TAG = 'ibexa.page_builder.site_access.list_resolver.strategy';

    protected function getContainerExtensions(): array
    {
        return [
            new IbexaPageBuilderExtension(),
        ];
    }

    public function testSiteAccessListResolverStrategyInjection(): void
    {
        $this->load();

        $listResolverStrategyTag = self::SITE_ACCESS_LIST_RESOLVER_STRATEGY_TAG;

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            PageBuilderListResolverStrategy::class,
            $listResolverStrategyTag,
            ['priority' => 0]
        );

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            PageBuilderSiteAccessResolver::class,
            '$resolverStrategies',
            new TaggedIteratorArgument($listResolverStrategyTag)
        );
    }
}
