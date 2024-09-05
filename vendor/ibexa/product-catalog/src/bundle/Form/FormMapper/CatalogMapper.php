<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\FormMapper;

use Ibexa\Bundle\ProductCatalog\Form\Data\Catalog\CatalogUpdateData;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\ProductCatalog\CatalogServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\CatalogInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CatalogMapper
{
    private CatalogServiceInterface $catalogService;

    public function __construct(
        CatalogServiceInterface $catalogService
    ) {
        $this->catalogService = $catalogService;
    }

    /**
     * @phpstan-param array{
     *     baseLanguage?: \Ibexa\Contracts\Core\Repository\Values\Content\Language|null,
     *     language: \Ibexa\Contracts\Core\Repository\Values\Content\Language,
     * } $params
     */
    public function mapToFormData(CatalogInterface $catalog, array $params): CatalogUpdateData
    {
        $optionsResolver = new OptionsResolver();
        $this->configureOptions($optionsResolver);
        $params = $optionsResolver->resolve($params);

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Language $language */
        $language = $params['language'];

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Language|null $baseLanguage */
        $baseLanguage = $params['baseLanguage'] ?? null;

        if ($baseLanguage) {
            $catalog = $this->catalogService->getCatalog(
                $catalog->getId(),
                [$baseLanguage->languageCode]
            );
        } else {
            $catalog = $this->catalogService->getCatalog(
                $catalog->getId(),
                [$language->languageCode]
            );
        }

        return CatalogUpdateData::createFromCatalog($catalog, $language);
    }

    /**
     * @throws \Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    private function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver
            ->setRequired(['language'])
            ->setDefined(['baseLanguage'])
            ->setAllowedTypes('baseLanguage', ['null', Language::class])
            ->setAllowedTypes('language', ['null', Language::class]);
    }
}
