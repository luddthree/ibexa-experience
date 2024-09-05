<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type;

use Ibexa\Bundle\ProductCatalog\Form\Data\CurrencySwitchData;
use Ibexa\Contracts\ProductCatalog\Values\Currency\Query\Criterion\IsCurrencyEnabledCriterion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CurrencySwitchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('currency', CurrencyChoiceType::class, [
            'criterion' => new IsCurrencyEnabledCriterion(),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'data_class' => CurrencySwitchData::class,
            'method' => Request::METHOD_GET,
        ]);
    }
}
