<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Seo\Templating\Twig;

use Ibexa\Bundle\Seo\Templating\Twig\Functions\SeoRenderer;
use Ibexa\Bundle\Seo\Templating\Twig\SeoExtension;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Seo\Renderer\TagRendererRegistryInterface;
use Ibexa\Contracts\Seo\Resolver\FieldReferenceResolverInterface;
use Ibexa\Contracts\Seo\Resolver\SeoTypesResolverInterface;
use Ibexa\Seo\Content\SeoFieldResolverInterface;
use Twig\Environment;
use Twig\Extension\RuntimeExtensionInterface;
use Twig\RuntimeLoader\RuntimeLoaderInterface;
use Twig\Test\IntegrationTestCase;

final class SeoExtensionTest extends IntegrationTestCase
{
    /** @var \Ibexa\Seo\Content\SeoFieldResolverInterface&\PHPUnit\Framework\MockObject\MockObject */
    private SeoFieldResolverInterface $seoFieldResolver;

    protected function setUp(): void
    {
        $this->seoFieldResolver = $this->createMock(SeoFieldResolverInterface::class);
    }

    /**
     * @return \Twig\RuntimeLoader\RuntimeLoaderInterface[]
     */
    protected function getRuntimeLoaders(): array
    {
        $seoRenderer = $this->createSeoRenderer();

        return [
            new class($seoRenderer) implements RuntimeLoaderInterface {
                private SeoRenderer $seoRenderer;

                public function __construct(SeoRenderer $seoRenderer)
                {
                    $this->seoRenderer = $seoRenderer;
                }

                public function load(string $class): ?RuntimeExtensionInterface
                {
                    if ($class === SeoRenderer::class) {
                        return $this->seoRenderer;
                    }

                    return null;
                }
            },
        ];
    }

    protected function getFixturesDir(): string
    {
        return __DIR__ . '/Fixtures';
    }

    /**
     * @return \Twig\Extension\ExtensionInterface[]
     */
    protected function getExtensions(): array
    {
        return [
            new SeoExtension(),
        ];
    }

    public function createContent(bool $withSeo): Content
    {
        $contentWithSeo = $this->createMock(Content::class);
        $contentWithoutSeo = $this->createMock(Content::class);

        $this->seoFieldResolver
            ->method('getSeoField')
            ->willReturnMap([
                [$contentWithSeo, $this->createMock(Field::class)],
                [$contentWithoutSeo, null],
            ]);

        return $withSeo ? $contentWithSeo : $contentWithoutSeo;
    }

    private function createSeoRenderer(): SeoRenderer
    {
        return new SeoRenderer(
            $this->createMock(SeoTypesResolverInterface::class),
            $this->createMock(FieldReferenceResolverInterface::class),
            $this->createMock(TagRendererRegistryInterface::class),
            $this->seoFieldResolver,
            $this->createMock(Environment::class),
        );
    }
}
