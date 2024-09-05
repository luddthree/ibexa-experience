<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type\Currency;

use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CurrencyStatusChoiceType extends AbstractType implements TranslationContainerInterface
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => [
                true => true,
                false => false,
            ],
            'choice_label' => static function ($choice, string $key, ?string $value): ?string {
                if ($value !== null) {
                    return 'currency.enabled.value.' . $value;
                }

                return null;
            },
            'translation_domain' => 'ibexa_product_catalog',
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public static function getTranslationMessages(): array
    {
        return [
            Message::create(
                'currency.enabled.value.0',
                'ibexa_product_catalog'
            )->setDesc('Disabled'),
            Message::create(
                'currency.enabled.value.1',
                'ibexa_product_catalog'
            )->setDesc('Enabled'),
        ];
    }
}
