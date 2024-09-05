<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\FieldType\Field;

use Ibexa\Contracts\FormBuilder\FieldType\Model\Field;
use Ibexa\Contracts\FormBuilder\FieldType\Model\FieldValue;

interface FieldHandlerInterface
{
    /**
     * @param \Ibexa\Contracts\FormBuilder\FieldType\Model\Field $field
     * @param \Ibexa\Contracts\FormBuilder\FieldType\Model\FieldValue $value
     *
     * @return \Ibexa\Contracts\FormBuilder\FieldType\Model\FieldValue
     */
    public function storeFieldData(Field $field, FieldValue $value): FieldValue;

    /**
     * @param \Ibexa\Contracts\FormBuilder\FieldType\Model\Field $field
     * @param \Ibexa\Contracts\FormBuilder\FieldType\Model\FieldValue $value
     *
     * @return \Ibexa\Contracts\FormBuilder\FieldType\Model\FieldValue
     */
    public function getFieldData(Field $field, FieldValue $value): FieldValue;

    /**
     * @param \Ibexa\Contracts\FormBuilder\FieldType\Model\Field $field
     * @param \Ibexa\Contracts\FormBuilder\FieldType\Model\FieldValue $value
     */
    public function deleteFieldData(Field $field, FieldValue $value): void;

    /**
     * Returns identifier of the field definition supported by the mapper.
     *
     * @return string
     */
    public function getSupportedField(): string;
}

class_alias(FieldHandlerInterface::class, 'EzSystems\EzPlatformFormBuilder\FieldType\Field\FieldHandlerInterface');
