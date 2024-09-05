<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Attribute;

use Ibexa\Contracts\ProductCatalog\Local\Attribute\VariantFormMapperInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionAssignmentInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

final class CheckboxVariantFormMapper implements VariantFormMapperInterface
{
    public function createForm(
        string $name,
        FormBuilderInterface $builder,
        AttributeDefinitionAssignmentInterface $assignment,
        array $context = []
    ): void {
        $definition = $assignment->getAttributeDefinition();

        $builder->add($name, ChoiceType::class, [
            'choices' => [
                'checkbox.value.true' => true,
                'checkbox.value.false' => false,
            ],
            'label' => $definition->getName(),
            'multiple' => true,
            'translation_domain' => 'ibexa_product_catalog',
        ]);
    }
}
