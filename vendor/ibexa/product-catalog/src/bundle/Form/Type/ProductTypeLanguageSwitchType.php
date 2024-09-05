<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type;

use Ibexa\Bundle\ProductCatalog\Form\Data\ProductLanguageSwitchData;
use Ibexa\Contracts\ProductCatalog\Values\ContentTypeAwareProductTypeInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ProductTypeLanguageSwitchType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductLanguageSwitchData::class,
            'languages' => static function (Options $options): array {
                return $options['product_type']->getContentType()->languageCodes;
            },
        ]);

        $resolver->setRequired('product_type');
        $resolver->setAllowedTypes('product_type', ContentTypeAwareProductTypeInterface::class);
    }

    public function getParent(): string
    {
        return LanguageSwitchType::class;
    }
}
