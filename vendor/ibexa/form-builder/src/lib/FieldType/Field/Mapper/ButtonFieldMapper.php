<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\FieldType\Field\Mapper;

use Ibexa\Contracts\FormBuilder\FieldType\Model\Field;

class ButtonFieldMapper extends GenericFieldMapper
{
    /**
     * {@inheritdoc}
     */
    protected function mapFormOptions(Field $field, array $constraints): array
    {
        return [
            'field' => $field,
            'label' => $field->getName(),
        ];
    }
}

class_alias(ButtonFieldMapper::class, 'EzSystems\EzPlatformFormBuilder\FieldType\Field\Mapper\ButtonFieldMapper');
