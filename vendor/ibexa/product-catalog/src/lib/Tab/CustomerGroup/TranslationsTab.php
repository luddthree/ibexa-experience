<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Tab\CustomerGroup;

use Ibexa\Bundle\ProductCatalog\Form\Data\CustomerGroup\Translation\TranslationAddData;
use Ibexa\Bundle\ProductCatalog\Form\Data\CustomerGroup\Translation\TranslationDeleteData;
use Ibexa\Bundle\ProductCatalog\Form\Type\CustomerGroup\Translation\TranslationAddType;
use Ibexa\Bundle\ProductCatalog\Form\Type\CustomerGroup\Translation\TranslationDeleteType;
use Ibexa\Bundle\ProductCatalog\UI\Language as UILanguage;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\CustomerGroup\Delete;
use Ibexa\Contracts\ProductCatalog\Values\TranslatableInterface;
use Ibexa\ProductCatalog\Tab\AbstractTranslationsTab;
use Symfony\Component\Form\FormInterface;

class TranslationsTab extends AbstractTranslationsTab
{
    public const URI_FRAGMENT = 'ibexa-tab-customer-group-translations';

    public function getTemplate(): string
    {
        return '@ibexadesign/product_catalog/customer_group/tab/translations.html.twig';
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function evaluate(array $parameters): bool
    {
        return $parameters['customer_group'] instanceof TranslatableInterface;
    }

    public function getTemplateParameters(array $contextParameters = []): array
    {
        /** @var \Ibexa\ProductCatalog\Local\Repository\Values\CustomerGroup $customerGroup */
        $customerGroup = $contextParameters['customer_group'];
        $translationsDataset = $this->getDataset($customerGroup);
        $translationAddForm = $this->createTranslationAddForm($customerGroup);

        $translationDeleteForm = $this->createTranslationDeleteForm(
            $customerGroup,
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
     * @param \Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface&\Ibexa\Contracts\ProductCatalog\Values\TranslatableInterface $customerGroup
     *
     * @return array<int, \Ibexa\Bundle\ProductCatalog\UI\Language>
     */
    private function getDataset(TranslatableInterface $customerGroup): array
    {
        /** @var array<string> $customerGroupLanguages */
        $customerGroupLanguages = $customerGroup->getLanguages();
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Language[] $languages */
        $languages = $this->languageService->loadLanguageListByCode($customerGroupLanguages);

        return array_map(
            fn (Language $language): UILanguage => $this->createLanguageFromCustomerGroup($language, $customerGroup),
            $languages
        );
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface&\Ibexa\Contracts\ProductCatalog\Values\TranslatableInterface $customerGroup
     */
    private function createLanguageFromCustomerGroup(
        Language $language,
        TranslatableInterface $customerGroup
    ): UILanguage {
        return new UILanguage($language, [
            'userCanRemove' => $this->permissionResolver->canUser(new Delete($customerGroup)),
        ]);
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface&\Ibexa\Contracts\ProductCatalog\Values\TranslatableInterface $customerGroup
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    private function createTranslationAddForm(TranslatableInterface $customerGroup): FormInterface
    {
        $data = new TranslationAddData($customerGroup);

        return $this->formFactory->createNamed('add-translation', TranslationAddType::class, $data);
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface&\Ibexa\Contracts\ProductCatalog\Values\TranslatableInterface $customerGroup
     * @param array<int, string> $languageCodes
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    private function createTranslationDeleteForm(
        TranslatableInterface $customerGroup,
        array $languageCodes
    ): FormInterface {
        $data = new TranslationDeleteData(
            $customerGroup,
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
