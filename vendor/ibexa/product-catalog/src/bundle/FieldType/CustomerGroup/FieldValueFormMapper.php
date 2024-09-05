<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\FieldType\CustomerGroup;

use Ibexa\Bundle\ProductCatalog\Form\Type\CustomerGroupChoiceType;
use Ibexa\ContentForms\Data\ContentTranslationData;
use Ibexa\Contracts\ContentForms\Data\Content\FieldData;
use Ibexa\Contracts\ContentForms\FieldType\FieldValueFormMapperInterface;
use Symfony\Component\Form\FormInterface;

final class FieldValueFormMapper implements FieldValueFormMapperInterface
{
    public function mapFieldValueForm(FormInterface $fieldForm, FieldData $data): void
    {
        $definition = $data->fieldDefinition;
        $rootForm = $fieldForm->getRoot();
        $isTranslation = $rootForm->getData() instanceof ContentTranslationData;

        $fieldForm->add('value', CustomerGroupChoiceType::class, [
            'required' => $definition->isRequired,
            'label' => $definition->getName(),
            'disabled' => $isTranslation,
        ]);
    }
}
