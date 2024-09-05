<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Attribute;

use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\StringToArrayTransformer;
use Ibexa\Bundle\ProductCatalog\Form\Type\TagifyType;
use Ibexa\Contracts\ProductCatalog\Local\Attribute\VariantFormMapperInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionAssignmentInterface;
use Symfony\Component\Form\Extension\Core\DataTransformer\IntegerToLocalizedStringTransformer;
use Symfony\Component\Form\FormBuilderInterface;

final class IntegerVariantFormMapper implements VariantFormMapperInterface
{
    public function createForm(
        string $name,
        FormBuilderInterface $builder,
        AttributeDefinitionAssignmentInterface $assignment,
        array $context = []
    ): void {
        $form = $builder->create($name, TagifyType::class, [
            'label' => $assignment->getAttributeDefinition()->getName(),
        ]);

        $form->addModelTransformer(new StringToArrayTransformer(new IntegerToLocalizedStringTransformer()));

        $builder->add($form);
    }
}
