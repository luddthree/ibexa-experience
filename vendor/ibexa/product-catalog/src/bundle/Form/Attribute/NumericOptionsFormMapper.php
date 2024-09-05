<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Attribute;

use Ibexa\Bundle\ProductCatalog\Validator\Constraints\AttributeDefinitionOptions;
use Ibexa\Contracts\ProductCatalog\Local\Attribute\OptionsFormMapperInterface;
use Symfony\Component\Form\FormBuilderInterface;

final class NumericOptionsFormMapper implements OptionsFormMapperInterface
{
    private string $formType;

    public function __construct(string $formType)
    {
        $this->formType = $formType;
    }

    public function createOptionsForm(string $name, FormBuilderInterface $builder, array $context = []): void
    {
        $builder->add($name, $this->formType, [
            'constraints' => [
                new AttributeDefinitionOptions(['type' => $context['type']]),
            ],
            'translation_mode' => $context['translation_mode'],
        ]);
    }
}
