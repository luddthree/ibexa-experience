<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type;

use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ProductTypeDefinitionChoiceType extends AbstractType implements TranslationContainerInterface
{
    public const PHYSICAL = 'physical';
    public const VIRTUAL = 'virtual';

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'choice_loader' => ChoiceList::lazy(
                    $this,
                    $this->getChoiceList()
                ),
            ]
        );
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    private function getChoiceList(): callable
    {
        return static function () {
            return [
                'product_type.physical' => self::PHYSICAL,
                'product_type.virtual' => self::VIRTUAL,
            ];
        };
    }

    public static function getTranslationMessages(): array
    {
        return [
            Message::create('product_type.physical', 'ibexa_product_catalog')->setDesc('Physical'),
            Message::create('product_type.virtual', 'ibexa_product_catalog')->setDesc('Virtual'),
        ];
    }
}
