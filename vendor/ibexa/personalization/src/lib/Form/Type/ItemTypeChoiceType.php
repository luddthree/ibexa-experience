<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Type;

use Ibexa\Personalization\Value\Content\AbstractItemType;
use Ibexa\Personalization\Value\Content\ItemType;
use Ibexa\Personalization\Value\Content\ItemTypeList;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ItemTypeChoiceType extends AbstractType
{
    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setAllowedTypes('choices', ItemTypeList::class);
        $resolver->setDefaults(
            [
                'choice_label' => static function (?AbstractItemType $itemType): string {
                    if (null === $itemType) {
                        return '';
                    }

                    return (string) $itemType;
                },
                'choice_attr' => static function (?AbstractItemType $itemType) {
                    return ['data-output-type-id' => $itemType instanceof ItemType
                        ? $itemType->getId()
                        : null,
                    ];
                },
            ]
        );
    }
}
