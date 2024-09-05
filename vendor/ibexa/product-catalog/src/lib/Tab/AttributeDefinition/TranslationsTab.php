<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Tab\AttributeDefinition;

use Ibexa\Bundle\ProductCatalog\Form\Data\AttributeDefinition\Translation\TranslationAddData;
use Ibexa\Bundle\ProductCatalog\Form\Data\AttributeDefinition\Translation\TranslationDeleteData;
use Ibexa\Bundle\ProductCatalog\Form\Type\AttributeDefinition\Translation\TranslationAddType;
use Ibexa\Bundle\ProductCatalog\Form\Type\AttributeDefinition\Translation\TranslationDeleteType;
use Ibexa\Bundle\ProductCatalog\UI\Language as UILanguage;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\AttributeDefinition\Delete;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Ibexa\Contracts\ProductCatalog\Values\TranslatableInterface;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeDefinition;
use Ibexa\ProductCatalog\Tab\AbstractTranslationsTab;
use Symfony\Component\Form\FormInterface;

class TranslationsTab extends AbstractTranslationsTab
{
    public const URI_FRAGMENT = 'ibexa-tab-attribute-definition-translations';

    public function getTemplate(): string
    {
        return '@ibexadesign/product_catalog/attribute_definition/tab/translations.html.twig';
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function evaluate(array $parameters): bool
    {
        return $parameters['attribute_definition'] instanceof TranslatableInterface;
    }

    public function getTemplateParameters(array $contextParameters = []): array
    {
        /** @var \Ibexa\ProductCatalog\Local\Repository\Values\AttributeDefinition&\Ibexa\Contracts\ProductCatalog\Values\TranslatableInterface $attributeDefinition */
        $attributeDefinition = $contextParameters['attribute_definition'];
        $translationsDataset = $this->getDataset($attributeDefinition);
        $translationAddForm = $this->createTranslationAddForm($attributeDefinition);

        $translationDeleteForm = $this->createTranslationDeleteForm(
            $attributeDefinition,
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
    private function getDataset(AttributeDefinition $attributeDefinition): array
    {
        /** @var array<string> $attributeDefinitionLanguages */
        $attributeDefinitionLanguages = $attributeDefinition->getLanguages();
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Language[] $languages */
        $languages = $this->languageService->loadLanguageListByCode($attributeDefinitionLanguages);

        return array_map(
            fn (Language $language): UILanguage => $this->createLanguageFromAttributeDefinition($language, $attributeDefinition),
            $languages
        );
    }

    private function createLanguageFromAttributeDefinition(
        Language $language,
        AttributeDefinitionInterface $attributeDefinition
    ): UILanguage {
        return new UILanguage($language, [
            'userCanRemove' => $this->permissionResolver->canUser(new Delete($attributeDefinition)),
        ]);
    }

    /**
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    private function createTranslationAddForm(AttributeDefinitionInterface $attributeDefinition): FormInterface
    {
        $data = new TranslationAddData($attributeDefinition);

        return $this->formFactory->createNamed('add-translation', TranslationAddType::class, $data);
    }

    /**
     * @param array<int, string> $languageCodes
     *
     * @phpstan-param \Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface&\Ibexa\Contracts\ProductCatalog\Values\TranslatableInterface $attributeDefinition
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    private function createTranslationDeleteForm(
        AttributeDefinitionInterface $attributeDefinition,
        array $languageCodes
    ): FormInterface {
        $data = new TranslationDeleteData(
            $attributeDefinition,
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
