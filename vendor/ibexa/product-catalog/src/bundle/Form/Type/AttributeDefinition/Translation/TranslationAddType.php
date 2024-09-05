<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type\AttributeDefinition\Translation;

use Ibexa\AdminUi\Form\Type\ChoiceList\Loader\AvailableTranslationLanguageChoiceLoader;
use Ibexa\AdminUi\Form\Type\ChoiceList\Loader\BaseTranslationLanguageChoiceLoader;
use Ibexa\Bundle\ProductCatalog\Form\Data\AttributeDefinition\Translation\TranslationAddData;
use Ibexa\Bundle\ProductCatalog\Form\Type\AttributeDefinitionReferenceType;
use Ibexa\Bundle\ProductCatalog\Form\Type\LanguageChoiceType;
use Ibexa\Contracts\Core\Repository\LanguageService;
use Ibexa\Contracts\ProductCatalog\AttributeDefinitionServiceInterface;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TranslationAddType extends AbstractType
{
    private LanguageService $languageService;

    private AttributeDefinitionServiceInterface $attributeDefinitionService;

    public function __construct(
        LanguageService $languageService,
        AttributeDefinitionServiceInterface $attributeDefinitionService
    ) {
        $this->languageService = $languageService;
        $this->attributeDefinitionService = $attributeDefinitionService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'attribute_definition',
                AttributeDefinitionReferenceType::class,
                ['label' => false]
            )
            ->add(
                'add',
                SubmitType::class,
                [
                    'label' => /** @Desc("Create") */ 'attribute_definition_translation_add_form.add',
                ]
            )
            ->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'onPreSetData'])
            ->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'onPreSubmit']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TranslationAddData::class,
            'translation_domain' => 'ibexa_product_catalog',
        ]);
    }

    /**
     * Adds language fields and populates options list based on default form data.
     *
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function onPreSetData(FormEvent $event): void
    {
        $form = $event->getForm();
        /** @var \Ibexa\Bundle\ProductCatalog\Form\Data\AttributeDefinition\Translation\TranslationAddData $data */
        $data = $event->getData();
        /** @var \Ibexa\ProductCatalog\Local\Repository\Values\AttributeDefinition $attributeDefinition */
        $attributeDefinition = $data->getAttributeDefinition();

        $languages = [];
        if (null !== $attributeDefinition) {
            $languages = $attributeDefinition->getLanguages();
        }

        $this->addLanguageFields($form, $languages);
    }

    /**
     * Adds language fields and populates options list based on submitted form data.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function onPreSubmit(FormEvent $event): void
    {
        $languages = [];
        $form = $event->getForm();
        $data = $event->getData();

        if (isset($data['attribute_definition'])) {
            /** @var \Ibexa\ProductCatalog\Local\Repository\Values\AttributeDefinition $attributeDefinition */
            $attributeDefinition = $this->attributeDefinitionService->getAttributeDefinition($data['attribute_definition']);

            $languages = $attributeDefinition->getLanguages();
        }

        $this->addLanguageFields($form, $languages);
    }

    /**
     * Adds language fields to the $form. Language options are composed based on content language.
     *
     * @param iterable<int, string> $languages
     */
    public function addLanguageFields(
        FormInterface $form,
        iterable $languages
    ): void {
        if (!is_array($languages)) {
            $languages = iterator_to_array($languages);
        }

        $form
            ->add(
                'language',
                LanguageChoiceType::class,
                [
                    'required' => true,
                    'placeholder' => false,
                    'choice_loader' => ChoiceList::loader(
                        $this,
                        new AvailableTranslationLanguageChoiceLoader($this->languageService, $languages),
                        $languages,
                    ),
                ]
            )
            ->add(
                'base_language',
                LanguageChoiceType::class,
                [
                    'required' => false,
                    'placeholder' => /** @Desc("Not selected") */ 'translation.base_language.no_language',
                    'choice_loader' => new BaseTranslationLanguageChoiceLoader($this->languageService, $languages),
                ]
            );
    }
}
