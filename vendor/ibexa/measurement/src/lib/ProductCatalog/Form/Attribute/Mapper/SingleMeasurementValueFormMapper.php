<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\ProductCatalog\Form\Attribute\Mapper;

use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionAssignmentInterface;
use Ibexa\Measurement\ProductCatalog\Form\Type\SingleAttributeMeasurementType;
use Symfony\Component\Form\FormBuilderInterface;

final class SingleMeasurementValueFormMapper extends AbstractMeasurementValueFormMapper
{
    protected function buildForm(
        string $name,
        FormBuilderInterface $builder,
        AttributeDefinitionAssignmentInterface $assignment,
        array $options
    ): void {
        $definition = $assignment->getAttributeDefinition();
        $definitionOptions = $definition->getOptions();

        /** @var float|null $default */
        $default = $definitionOptions->get('defaultValue');
        $default = isset($default) ? (float)$default : $default;

        $options = array_merge($options, [
            'sign' => $definitionOptions->get('sign'),
            'defaultValue' => $default,
        ]);
        $builder->add($name, SingleAttributeMeasurementType::class, $options);
    }
}
