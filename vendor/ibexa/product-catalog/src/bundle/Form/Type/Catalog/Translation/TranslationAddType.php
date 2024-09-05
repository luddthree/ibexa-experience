<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type\Catalog\Translation;

use Ibexa\AdminUi\Form\Type\ChoiceList\Loader\AvailableTranslationLanguageChoiceLoader;
use Ibexa\AdminUi\Form\Type\ChoiceList\Loader\BaseTranslationLanguageChoiceLoader;
use Ibexa\Bundle\ProductCatalog\Form\Data\Catalog\Translation\TranslationAddData;
use Ibexa\Bundle\ProductCatalog\Form\Type\LanguageChoiceType;
use Ibexa\Contracts\Core\Repository\LanguageService;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TranslationAddType extends AbstractType
{
    private LanguageService $languageService;

    public function __construct(
        LanguageService $languageService
    ) {
        $this->languageService = $languageService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'onPreSetData'])
        ;
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
        /** @var \Ibexa\Bundle\ProductCatalog\Form\Data\Catalog\Translation\TranslationAddData $data */
        $data = $event->getData();
        $languages = $data->getCatalog()->getLanguages();

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
                        [
                            'languages' => $languages,
                            'type' => AvailableTranslationLanguageChoiceLoader::class,
                        ],
                    ),
                ]
            )
            ->add(
                'base_language',
                LanguageChoiceType::class,
                [
                    'required' => false,
                    'placeholder' => /** @Desc("Not selected") */ 'translation.base_language.no_language',
                    'choice_loader' => ChoiceList::loader(
                        $this,
                        new BaseTranslationLanguageChoiceLoader($this->languageService, $languages),
                        [
                            'languages' => $languages,
                            'type' => BaseTranslationLanguageChoiceLoader::class,
                        ],
                    ),
                ]
            );
    }
}
