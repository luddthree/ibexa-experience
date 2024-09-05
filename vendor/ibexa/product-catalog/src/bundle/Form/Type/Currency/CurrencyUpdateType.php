<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type\Currency;

use Ibexa\Bundle\ProductCatalog\Form\Data\Currency\CurrencyUpdateData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CurrencyUpdateType extends AbstractType
{
    public function getParent(): string
    {
        return CurrencyType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CurrencyUpdateData::class,
        ]);
    }
}
