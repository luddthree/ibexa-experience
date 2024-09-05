<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Tab\AttributeGroup;

use Ibexa\Bundle\ProductCatalog\Form\Data\AttributeGroup\Translation\TranslationAddData;
use Ibexa\Bundle\ProductCatalog\Form\Data\AttributeGroup\Translation\TranslationDeleteData;
use Ibexa\Bundle\ProductCatalog\Form\Type\AttributeGroup\Translation\TranslationAddType;
use Ibexa\Bundle\ProductCatalog\Form\Type\AttributeGroup\Translation\TranslationDeleteType;
use Ibexa\Bundle\ProductCatalog\UI\Language as UILanguage;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\AttributeGroup\Delete;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;
use Ibexa\Contracts\ProductCatalog\Values\TranslatableInterface;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeGroup;
use Ibexa\ProductCatalog\Tab\AbstractTranslationsTab;
use Symfony\Component\Form\FormInterface;

class TranslationsTab extends AbstractTranslationsTab
{
    public const URI_FRAGMENT = 'ibexa-tab-attribute-group-translations';

    public function getTemplate(): string
    {
        return '@ibexadesign/product_catalog/attribute_group/tab/translations.html.twig';
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function evaluate(array $parameters): bool
    {
        return $parameters['attribute_group'] instanceof TranslatableInterface;
    }

    public function getTemplateParameters(array $contextParameters = []): array
    {
        /** @var \Ibexa\ProductCatalog\Local\Repository\Values\AttributeGroup&\Ibexa\Contracts\ProductCatalog\Values\TranslatableInterface $attributeGroup */
        $attributeGroup = $contextParameters['attribute_group'];
        $translationsDataset = $this->getDataset($attributeGroup);
        $translationAddForm = $this->createTranslationAddForm($attributeGroup);

        $translationDeleteForm = $this->createTranslationDeleteForm(
            $attributeGroup,
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
     * @return array<int, \Ibexa\Bundle\ProductCatalog\UI\Language>
     */
    private function getDataset(AttributeGroup $attributeGroup): array
    {
        $attributeGroupLanguages = $attributeGroup->getLanguages();
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Language[] $languages */
        $languages = $this->languageService->loadLanguageListByCode($attributeGroupLanguages);

        return array_map(
            fn (Language $language): UILanguage => $this->createLanguageFromAttributeGroup($language, $attributeGroup),
            $languages
        );
    }

    private function createLanguageFromAttributeGroup(
        Language $language,
        AttributeGroupInterface $attributeGroup
    ): UILanguage {
        return new UILanguage($language, [
            'userCanRemove' => $this->permissionResolver->canUser(new Delete($attributeGroup)),
        ]);
    }

    /**
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    private function createTranslationAddForm(AttributeGroupInterface $attributeGroup): FormInterface
    {
        $data = new TranslationAddData($attributeGroup);

        return $this->formFactory->createNamed('add-translation', TranslationAddType::class, $data);
    }

    /**
     * @param array<int, string> $languageCodes
     *
     * @phpstan-param \Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface&\Ibexa\Contracts\ProductCatalog\Values\TranslatableInterface $attributeGroup
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    private function createTranslationDeleteForm(
        AttributeGroupInterface $attributeGroup,
        array $languageCodes
    ): FormInterface {
        $data = new TranslationDeleteData(
            $attributeGroup,
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
