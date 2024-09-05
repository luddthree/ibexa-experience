<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Tab\Product;

use Ibexa\Bundle\ProductCatalog\Form\Type\ProductTranslationAddType;
use Ibexa\Bundle\ProductCatalog\Form\Type\ProductTranslationDeleteType;
use Ibexa\Bundle\ProductCatalog\UI\Language as UILanguage;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Product\Delete;
use Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\ProductCatalog\Local\Repository\Values\Product;
use Ibexa\ProductCatalog\Tab\AbstractTranslationsTab;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class TranslationsTab extends AbstractTranslationsTab
{
    public const URI_FRAGMENT = 'ibexa-tab-product-translations';

    public function getTemplate(): string
    {
        return '@ibexadesign/product_catalog/product/tab/translations.html.twig';
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function evaluate(array $parameters): bool
    {
        return $parameters['product'] instanceof Product;
    }

    public function getTemplateParameters(array $contextParameters = []): array
    {
        /** @var \Ibexa\Contracts\ProductCatalog\Values\ProductInterface&\Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface $product */
        $product = $contextParameters['product'];

        $viewParameters = [
            'translations' => $this->getTranslations($product),
            'form_translation_add' => $this->createTranslationAddForm($product)->createView(),
            'form_translation_remove' => $this->createTranslationDeleteForm($product)->createView(),
        ];

        return array_replace($contextParameters, $viewParameters);
    }

    /**
     * @return array<int, \Ibexa\Bundle\ProductCatalog\UI\Language>
     */
    private function getTranslations(ProductInterface $product): array
    {
        /** @var \Ibexa\Contracts\ProductCatalog\Values\ProductInterface&\Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface $product */
        $languageCodes = $product->getContent()->versionInfo->languageCodes;
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Language[] $languages */
        $languages = $this->languageService->loadLanguageListByCode($languageCodes);

        return array_map(
            fn (Language $language): UILanguage => $this->createTranslation($language, $product),
            $languages
        );
    }

    public function getOrder(): int
    {
        return 600;
    }

    private function createTranslation(Language $language, ProductInterface $product): UILanguage
    {
        return new UILanguage($language, [
            'userCanRemove' => $this->permissionResolver->canUser(new Delete($product)),
        ]);
    }

    private function createTranslationAddForm(ProductInterface $product): FormInterface
    {
        $actionUrl = $this->urlGenerator->generate(
            'ibexa.product_catalog.product.translation.create',
            [
                'productCode' => $product->getCode(),
            ]
        );

        return $this->formFactory->createNamed(
            'add-translation',
            ProductTranslationAddType::class,
            null,
            [
                'action' => $actionUrl,
                'method' => Request::METHOD_POST,
                'product' => $product,
            ]
        );
    }

    private function createTranslationDeleteForm(ContentAwareProductInterface $product): FormInterface
    {
        $actionUrl = $this->urlGenerator->generate(
            'ibexa.product_catalog.product.translation.delete',
            [
                'productCode' => $product->getCode(),
            ]
        );

        $isTranslationDeleteFormDisabled = count($product->getContent()->versionInfo->languageCodes) === 1;

        return $this->formFactory->createNamed(
            'delete-translations',
            ProductTranslationDeleteType::class,
            null,
            [
                'action' => $actionUrl,
                'method' => Request::METHOD_POST,
                'disabled' => $isTranslationDeleteFormDisabled,
            ]
        );
    }
}
