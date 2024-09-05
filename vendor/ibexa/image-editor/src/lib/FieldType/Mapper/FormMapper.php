<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ImageEditor\FieldType\Mapper;

use Ibexa\ContentForms\FieldType\DataTransformer\ImageValueTransformer;
use Ibexa\ContentForms\Form\Type\FieldType\ImageFieldType;
use Ibexa\Contracts\ContentForms\Data\Content\FieldData;
use Ibexa\Contracts\ContentForms\FieldType\FieldValueFormMapperInterface;
use Ibexa\Contracts\Core\Repository\FieldTypeService;
use Ibexa\Core\FieldType\Image\Value;
use Symfony\Component\Form\FormInterface;

final class FormMapper implements FieldValueFormMapperInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\FieldTypeService */
    private $fieldTypeService;

    public function __construct(FieldTypeService $fieldTypeService)
    {
        $this->fieldTypeService = $fieldTypeService;
    }

    public function mapFieldValueForm(FormInterface $fieldForm, FieldData $data): void
    {
        $fieldDefinition = $data->fieldDefinition;
        $formConfig = $fieldForm->getConfig();
        $fieldType = $this->fieldTypeService->getFieldType($fieldDefinition->fieldTypeIdentifier);
        $isAlternativeTextRequired = $fieldDefinition->validatorConfiguration['AlternativeTextValidator']['required'] ?? false;
        $mimeTypes = $fieldDefinition->fieldSettings['mimeTypes'] ?? [];

        $fieldForm
            ->add(
                $formConfig->getFormFactory()->createBuilder()
                    ->create(
                        'value',
                        ImageFieldType::class,
                        [
                            'required' => $fieldDefinition->isRequired,
                            'label' => $fieldDefinition->getName(),
                            'file_name' => $data->value->fileName,
                            'use_base64' => true,
                            'is_alternative_text_required' => $isAlternativeTextRequired,
                            'mime_types' => $mimeTypes,
                        ]
                    )
                    ->addModelTransformer(
                        new ImageValueTransformer(
                            $fieldType,
                            $data->value,
                            Value::class,
                        )
                    )
                    ->setAutoInitialize(false)
                    ->getForm()
            );
    }
}

class_alias(FormMapper::class, 'Ibexa\Platform\ImageEditor\FieldType\Mapper\FormMapper');
