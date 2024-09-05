<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Tab\Catalog;

use Ibexa\Bundle\ProductCatalog\Form\Data\Catalog\Translation\TranslationAddData;
use Ibexa\Bundle\ProductCatalog\Form\Data\Catalog\Translation\TranslationDeleteData;
use Ibexa\Bundle\ProductCatalog\Form\Type\Catalog\Translation\TranslationAddType;
use Ibexa\Bundle\ProductCatalog\Form\Type\Catalog\Translation\TranslationDeleteType;
use Ibexa\Bundle\ProductCatalog\UI\Language as UILanguage;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Catalog\Delete;
use Ibexa\Contracts\ProductCatalog\Values\CatalogInterface;
use Ibexa\Contracts\ProductCatalog\Values\TranslatableInterface;
use Ibexa\ProductCatalog\Tab\AbstractTranslationsTab;
use Symfony\Component\Form\FormInterface;

class TranslationsTab extends AbstractTranslationsTab
{
    public const URI_FRAGMENT = 'ibexa-tab-catalog-translations';

    public function getTemplate(): string
    {
        return '@ibexadesign/product_catalog/catalog/tab/translations.html.twig';
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function evaluate(array $parameters): bool
    {
        return $parameters['catalog'] instanceof TranslatableInterface;
    }

    public function getTemplateParameters(array $contextParameters = []): array
    {
        /** @var \Ibexa\ProductCatalog\Local\Repository\Values\Catalog\Catalog $catalog */
        $catalog = $contextParameters['catalog'];
        $translationsDataset = $this->getDataset($catalog);
        $translationAddForm = $this->createTranslationAddForm($catalog);

        $translationDeleteForm = $this->createTranslationDeleteForm(
            $catalog,
            array_column($translationsDataset, 'languageCode')
        );

        $viewParameters = [
            'translations' => $translationsDataset,
            'form_translation_add' => $translationAddForm->createView(),
            'form_translation_remove' => $translationDeleteForm->createView(),
        ];

        return array_replace($contextParameters, $viewParameters);
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\CatalogInterface&\Ibexa\Contracts\ProductCatalog\Values\TranslatableInterface $catalog
     *
     * @return array<int, \Ibexa\Bundle\ProductCatalog\UI\Language>
     */
    private function getDataset(TranslatableInterface $catalog): array
    {
        /** @var array<string> $catalogLanguages */
        $catalogLanguages = $catalog->getLanguages();
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Language[] $languages */
        $languages = $this->languageService->loadLanguageListByCode($catalogLanguages);

        return array_map(
            fn (Language $language): UILanguage => $this->createLanguageFromCatalog($language, $catalog),
            $languages
        );
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\CatalogInterface&\Ibexa\Contracts\ProductCatalog\Values\TranslatableInterface $catalog
     */
    private function createLanguageFromCatalog(
        Language $language,
        TranslatableInterface $catalog
    ): UILanguage {
        return new UILanguage($language, [
            'userCanRemove' => $this->permissionResolver->canUser(new Delete($catalog)),
        ]);
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\CatalogInterface&\Ibexa\Contracts\ProductCatalog\Values\TranslatableInterface $catalog
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    private function createTranslationAddForm(CatalogInterface $catalog): FormInterface
    {
        $data = new TranslationAddData($catalog);

        return $this->formFactory->createNamed('add-translation', TranslationAddType::class, $data);
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\CatalogInterface&\Ibexa\Contracts\ProductCatalog\Values\TranslatableInterface $catalog
     * @param array<int, string> $languageCodes
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    private function createTranslationDeleteForm(
        TranslatableInterface $catalog,
        array $languageCodes
    ): FormInterface {
        $data = new TranslationDeleteData(
            $catalog,
            array_fill_keys($languageCodes, false)
        );

        return $this->formFactory->createNamed(
            'delete-translations',
            TranslationDeleteType::class,
            $data,
            [
                'disabled' => count($languageCodes) === 1,
            ]
        );
    }
}
