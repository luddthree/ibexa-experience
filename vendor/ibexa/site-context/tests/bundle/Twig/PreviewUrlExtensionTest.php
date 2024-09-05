<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\SiteContext\Twig;

use Ibexa\Bundle\SiteContext\Twig\PreviewUrlExtension;
use Ibexa\Bundle\SiteContext\Twig\PreviewUrlRuntime;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\SiteContext\PreviewUrlResolver\LocationPreviewUrlResolverInterface;
use Twig\RuntimeLoader\RuntimeLoaderInterface;
use Twig\Test\IntegrationTestCase;

final class PreviewUrlExtensionTest extends IntegrationTestCase
{
    private const EXAMPLE_LOCATION_PREVIEW_URL = 'https://example.com/location-preview-url';

    private LocationPreviewUrlResolverInterface $locationPreviewUrlResolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->locationPreviewUrlResolver = $this->createMock(LocationPreviewUrlResolverInterface::class);
        $this->locationPreviewUrlResolver
            ->method('resolveUrl')
            ->with($this->createExampleLocation())
            ->willReturn(self::EXAMPLE_LOCATION_PREVIEW_URL);
    }

    protected function getExtensions(): array
    {
        return [
            new PreviewUrlExtension(),
        ];
    }

    protected function getRuntimeLoaders(): array
    {
        return [
            new class($this->locationPreviewUrlResolver) implements RuntimeLoaderInterface {
                private LocationPreviewUrlResolverInterface $locationPreviewUrlResolver;

                public function __construct(LocationPreviewUrlResolverInterface $locationPreviewUrlResolver)
                {
                    $this->locationPreviewUrlResolver = $locationPreviewUrlResolver;
                }

                public function load(string $class): ?object
                {
                    if (PreviewUrlRuntime::class === $class) {
                        return new PreviewUrlRuntime($this->locationPreviewUrlResolver);
                    }

                    return null;
                }
            },
        ];
    }

    public function getFixturesDir(): string
    {
        return __DIR__ . '/Fixtures/PreviewUrlExtension';
    }

    public function createExampleLocation(): Location
    {
        return $this->createMock(Location::class);
    }
}
