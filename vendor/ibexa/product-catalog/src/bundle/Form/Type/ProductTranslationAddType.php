<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type;

use Ibexa\Bundle\ProductCatalog\Form\Data\ProductTranslationAddData;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ProductTranslationAddType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var \Ibexa\Contracts\ProductCatalog\Values\ProductInterface&\Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface $product */
        $product = $options['product'];

        $builder->add(
            'language',
            LanguageChoiceType::class,
            [
                'required' => true,
                'placeholder' => false,
                'choice_filter' => static function (?Language $language) use ($product): bool {
                    if ($language !== null) {
                        return !in_array(
                            $language->languageCode,
                            $product->getContent()->versionInfo->languageCodes
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
                'choice_filter' => static function (?Language $language) use ($product): bool {
                    if ($language !== null) {
                        return in_array(
                            $language->languageCode,
                            $product->getContent()->versionInfo->languageCodes,
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
        $resolver->setRequired(['product']);
        $resolver->setAllowedTypes('product', [ProductInterface::class]);
        $resolver->setDefaults([
            'data_class' => ProductTranslationAddData::class,
            'translation_domain' => 'ibexa_product_catalog',
        ]);
    }
}
