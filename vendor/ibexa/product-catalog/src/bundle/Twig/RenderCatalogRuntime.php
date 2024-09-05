<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Twig;

use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\ProductCatalog\Config\ConfigProviderInterface;
use Ibexa\ProductCatalog\Local\Repository\Values\Catalog\Status;
use JMS\TranslationBundle\Annotation\Ignore;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\RuntimeExtensionInterface;

final class RenderCatalogRuntime implements RuntimeExtensionInterface, TranslationContainerInterface
{
    private const TRANSLATION_KEY_PLACE_PREFIX = 'catalog.place.';

    private TranslatorInterface $translator;

    private ConfigResolverInterface $configResolver;

    private ConfigProviderInterface $configProvider;

    private LocationService $locationService;

    public function __construct(
        TranslatorInterface $translator,
        ConfigResolverInterface $configResolver,
        ConfigProviderInterface $configProvider,
        LocationService $locationService
    ) {
        $this->translator = $translator;
        $this->configResolver = $configResolver;
        $this->configProvider = $configProvider;
        $this->locationService = $locationService;
    }

    public function renderCatalogStatus(string $status): string
    {
        return $this->translator->trans(
            /** @Ignore */
            self::TRANSLATION_KEY_PLACE_PREFIX . $status,
            [],
            'ibexa_product_catalog'
        );
    }

    public static function getTranslationMessages(): array
    {
        return [
            Message::create(
                self::TRANSLATION_KEY_PLACE_PREFIX . Status::DRAFT_PLACE,
                'ibexa_product_catalog'
            )->setDesc('Draft'),
            Message::create(
                self::TRANSLATION_KEY_PLACE_PREFIX . Status::PUBLISHED_PLACE,
                'ibexa_product_catalog'
            )->setDesc('Published'),
            Message::create(
                self::TRANSLATION_KEY_PLACE_PREFIX . Status::ARCHIVED_PLACE,
                'ibexa_product_catalog'
            )->setDesc('Archived'),
        ];
    }

    /**
     * @return string[]
     */
    public function getFilterPreviewTemplates(): array
    {
        $templates = $this->configResolver->getParameter('product_catalog.filter_preview_templates');

        usort(
            $templates,
            static fn (array $a, array $b): int => $b['priority'] <=> $a['priority']
        );

        return array_column($templates, 'template');
    }

    public function getProductCatalogRoot(): ?Location
    {
        $rootLocationRemoteId = $this->configProvider->getEngineOption(
            'root_location_remote_id'
        );

        return $rootLocationRemoteId !== null
            ? $this->locationService->loadLocationByRemoteId($rootLocationRemoteId)
            : null;
    }

    public function isPimLocal(): bool
    {
        return $this->configProvider->getEngineType() === 'local';
    }
}
