<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type;

use Ibexa\Contracts\ProductCatalog\AttributeTypeServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeTypeInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Traversable;

final class AttributeTypeChoiceType extends AbstractType
{
    private AttributeTypeServiceInterface $attributeTypeRegistry;

    public function __construct(AttributeTypeServiceInterface $attributeTypeRegistry)
    {
        $this->attributeTypeRegistry = $attributeTypeRegistry;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => $this->getChoices(),
            'choice_value' => 'identifier',
            'choice_label' => 'name',
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    /**
     * @return array<string,\Ibexa\Contracts\ProductCatalog\Values\AttributeTypeInterface>
     */
    private function getChoices(): array
    {
        $choices = $this->attributeTypeRegistry->getAttributeTypes();
        if ($choices instanceof Traversable) {
            $choices = iterator_to_array($choices);
        }

        uasort($choices, static function (AttributeTypeInterface $a, AttributeTypeInterface $b): int {
            return strcmp($a->getName(), $b->getName());
        });

        return $choices;
    }
}
