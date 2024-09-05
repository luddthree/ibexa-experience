<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Tab\ProductType;

use Ibexa\Bundle\ProductCatalog\Form\Type\ProductTypeTranslationAddType;
use Ibexa\Bundle\ProductCatalog\Form\Type\ProductTypeTranslationDeleteType;
use Ibexa\Bundle\ProductCatalog\UI\Language as UILanguage;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\ProductType\Delete;
use Ibexa\Contracts\ProductCatalog\Values\ContentTypeAwareProductTypeInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Ibexa\ProductCatalog\Tab\AbstractTranslationsTab;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class TranslationTab extends AbstractTranslationsTab
{
    public const URI_FRAGMENT = 'ibexa-tab-product-type-translations';

    public function getTemplate(): string
    {
        return '@ibexadesign/product_catalog/product_type/tab/translations.html.twig';
    }

    public function getTemplateParameters(array $contextParameters = []): array
    {
        $productType = $contextParameters['product_type'];

        return array_replace($contextParameters, [
            'translations' => $this->getTranslations($productType),
            'form_translation_add' => $this->createTranslationAddForm($productType)->createView(),
            'form_translation_remove' => $this->createTranslationDeleteForm($productType)->createView(),
        ]);
    }

    public function getIdentifier(): string
    {
        return 'translations';
    }

    public function evaluate(array $parameters): bool
    {
        return $parameters['product_type'] instanceof ContentTypeAwareProductTypeInterface;
    }

    public function getOrder(): int
    {
        return 500;
    }

    /**
     * @return array<int, \Ibexa\Bundle\ProductCatalog\UI\Language>
     */
    private function getTranslations(ContentTypeAwareProductTypeInterface $productType): iterable
    {
        $languageCodes = $productType->getContentType()->languageCodes;
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Language[] $languages */
        $languages = $this->languageService->loadLanguageListByCode($languageCodes);

        return array_map(
            fn (Language $language): UILanguage => $this->createTranslation($language, $productType),
            $languages
        );
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\ContentTypeAwareProductTypeInterface $productType
     */
    private function createTranslation(Language $language, ProductTypeInterface $productType): UILanguage
    {
        return new UILanguage($language, [
            'userCanRemove' => $this->permissionResolver->canUser(new Delete($productType)),
            'isMain' => $language->languageCode === $productType->getContentType()->mainLanguageCode,
        ]);
    }

    private function createTranslationAddForm(ContentTypeAwareProductTypeInterface $productType): FormInterface
    {
        $actionUrl = $this->urlGenerator->generate(
            'ibexa.product_catalog.product_type.translation.create',
            [
                'productTypeIdentifier' => $productType->getIdentifier(),
            ]
        );

        return $this->formFactory->createNamed(
            'add-translation',
            ProductTypeTranslationAddType::class,
            null,
            [
                'action' => $actionUrl,
                'method' => Request::METHOD_POST,
                'product_type' => $productType,
            ]
        );
    }

    private function createTranslationDeleteForm(ContentTypeAwareProductTypeInterface $productType): FormInterface
    {
        $actionUrl = $this->urlGenerator->generate(
            'ibexa.product_catalog.product_type.translation.delete',
            [
                'productTypeIdentifier' => $productType->getIdentifier(),
            ]
        );

        return $this->formFactory->createNamed(
            'delete-translations',
            ProductTypeTranslationDeleteType::class,
            null,
            [
                'action' => $actionUrl,
                'method' => Request::METHOD_POST,
                'disabled' => count($productType->getContentType()->languageCodes) === 1,
            ]
        );
    }
}
