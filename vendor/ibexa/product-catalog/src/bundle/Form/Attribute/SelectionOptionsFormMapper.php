<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Attribute;

use Ibexa\Bundle\ProductCatalog\Form\Type\SelectionAttributeOptionsType;
use Ibexa\Bundle\ProductCatalog\Validator\Constraints\AttributeDefinitionOptions;
use Ibexa\Contracts\ProductCatalog\Local\Attribute\OptionsFormMapperInterface;
use Symfony\Component\Form\FormBuilderInterface;

final class SelectionOptionsFormMapper implements OptionsFormMapperInterface
{
    public function createOptionsForm(string $name, FormBuilderInterface $builder, array $context = []): void
    {
        $builder->add($name, SelectionAttributeOptionsType::class, [
            'constraints' => [
                new AttributeDefinitionOptions(['type' => $context['type']]),
            ],
            'language_code' => $context['language_code'],
            'translation_mode' => $context['translation_mode'],
        ]);
    }
}
