<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Attribute;

use Ibexa\Bundle\ProductCatalog\Validator\Constraints\AttributeValue;
use Ibexa\Contracts\ProductCatalog\Local\Attribute\ValueFormMapperInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionAssignmentInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class IntegerValueFormMapper implements ValueFormMapperInterface
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
            'required' => $assignment->isRequired(),
            'constraints' => [
                new AttributeValue([
                    'definition' => $definition,
                ]),
            ],
        ];

        if ($assignment->isRequired()) {
            $options['constraints'][] = new Assert\NotBlank();
        }

        $builder->add($name, IntegerType::class, $options);
    }
}
