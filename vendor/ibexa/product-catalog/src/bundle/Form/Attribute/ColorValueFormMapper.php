<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Attribute;

use Ibexa\Contracts\ProductCatalog\Local\Attribute\ValueFormMapperInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionAssignmentInterface;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class ColorValueFormMapper implements ValueFormMapperInterface
{
    public function createValueForm(
        string $name,
        FormBuilderInterface $builder,
        AttributeDefinitionAssignmentInterface $assignment,
        array $context = []
    ): void {
        $definition = $assignment->getAttributeDefinition();

        $options = [
            'disabled' => $context['translation_mode'] ?? false,
            'label' => $definition->getName(),
            'block_prefix' => 'color_attribute_value',
            'required' => $assignment->isRequired(),
        ];

        if ($assignment->isRequired()) {
            $options['constraints'] = [
                new Assert\NotBlank(),
            ];
        }

        $builder->add($name, ColorType::class, $options);
    }
}
