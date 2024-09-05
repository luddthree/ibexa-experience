<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type;

use Ibexa\Bundle\ProductCatalog\Form\Data\ProductTypeTranslationAddData;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\ProductCatalog\Values\ContentTypeAwareProductTypeInterface;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ProductTypeTranslationAddType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var \Ibexa\Contracts\ProductCatalog\Values\ContentTypeAwareProductTypeInterface $productType */
        $productType = $options['product_type'];

        $builder->add(
            'language',
            LanguageChoiceType::class,
            [
                'required' => true,
                'placeholder' => false,
                'choice_filter' => static function (?Language $language) use ($productType): bool {
                    if ($language !== null) {
                        return !in_array(
                            $language->languageCode,
                            $productType->getContentType()->languageCodes
                        );
                    }

                    return false;
                },
            ]
        );

        $builder->add(
            'base_language',
            LanguageChoiceType::class,
            [
                'required' => false,
                'placeholder' => /** @Desc("Not selected") */ 'translation.base_language.no_language',
                'choice_filter' => static function (?Language $language) use ($productType): bool {
                    if ($language !== null) {
                        return in_array(
                            $language->languageCode,
                            $productType->getContentType()->languageCodes,
                            true
                        );
                    }

                    return false;
                },
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(['product_type']);
        $resolver->setAllowedTypes('product_type', [ContentTypeAwareProductTypeInterface::class]);
        $resolver->setDefaults([
            'data_class' => ProductTypeTranslationAddData::class,
            'translation_domain' => 'ibexa_product_catalog',
        ]);
    }
}
