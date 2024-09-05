<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type\AttributeGroup\Translation;

use EmptyIterator;
use Ibexa\AdminUi\Form\Type\ChoiceList\Loader\AvailableTranslationLanguageChoiceLoader;
use Ibexa\AdminUi\Form\Type\ChoiceList\Loader\BaseTranslationLanguageChoiceLoader;
use Ibexa\Bundle\ProductCatalog\Form\Data\AttributeGroup\Translation\TranslationAddData;
use Ibexa\Bundle\ProductCatalog\Form\Type\AttributeGroupReferenceType;
use Ibexa\Bundle\ProductCatalog\Form\Type\LanguageChoiceType;
use Ibexa\Contracts\Core\Repository\LanguageService;
use Ibexa\Contracts\ProductCatalog\AttributeGroupServiceInterface;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TranslationAddType extends AbstractType
{
    private LanguageService $languageService;

    private AttributeGroupServiceInterface $attributeGroupService;

    public function __construct(
        LanguageService $languageService,
        AttributeGroupServiceInterface $attributeGroupService
    ) {
        $this->languageService = $languageService;
        $this->attributeGroupService = $attributeGroupService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'attribute_group',
                AttributeGroupReferenceType::class,
                ['label' => false]
            )
            ->add(
                'add',
                SubmitType::class,
                [
                    'label' => /** @Desc("Create") */ 'attribute_group_translation_add_form.add',
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
        /** @var \Ibexa\Bundle\ProductCatalog\Form\Data\AttributeGroup\Translation\TranslationAddData $data */
        $data = $event->getData();
        /** @var \Ibexa\ProductCatalog\Local\Repository\Values\AttributeGroup $attributeGroup */
        $attributeGroup = $data->getAttributeGroup();

        $languages = new EmptyIterator();
        if (null !== $attributeGroup) {
            $languages = $attributeGroup->getLanguages();
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
        $languages = new EmptyIterator();
        $form = $event->getForm();
        $data = $event->getData();

        if (isset($data['attribute_group'])) {
            /** @var \Ibexa\ProductCatalog\Local\Repository\Values\AttributeGroup $attributeGroup */
            $attributeGroup = $this->attributeGroupService->getAttributeGroup($data['attribute_group']);

            $languages = $attributeGroup->getLanguages();
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
                    'choice_loader' => new AvailableTranslationLanguageChoiceLoader($this->languageService, $languages),
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
