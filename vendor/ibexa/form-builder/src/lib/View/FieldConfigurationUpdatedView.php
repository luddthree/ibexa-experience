<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\View;

use Ibexa\Contracts\FormBuilder\FieldType\Model\Field;
use Ibexa\Core\MVC\Symfony\View\BaseView;
use Ibexa\FormBuilder\Definition\FieldDefinition;

class FieldConfigurationUpdatedView extends BaseView
{
    /** @var \Ibexa\Contracts\FormBuilder\FieldType\Model\Field */
    public $field;

    /** @var \Ibexa\FormBuilder\Definition\FieldDefinition */
    public $fieldDefinition;

    /**
     * @return \Ibexa\Contracts\FormBuilder\FieldType\Model\Field
     */
    public function getField(): Field
    {
        return $this->field;
    }

    /**
     * @param \Ibexa\Contracts\FormBuilder\FieldType\Model\Field $field
     *
     * @return self
     */
    public function setField(Field $field): self
    {
        $this->field = $field;

        return $this;
    }

    /**
     * @return \Ibexa\FormBuilder\Definition\FieldDefinition
     */
    public function getFieldDefinition(): FieldDefinition
    {
        return $this->fieldDefinition;
    }

    /**
     * @param \Ibexa\FormBuilder\Definition\FieldDefinition $fieldDefinition
     *
     * @return self
     */
    public function setFieldDefinition(FieldDefinition $fieldDefinition): self
    {
        $this->fieldDefinition = $fieldDefinition;

        return $this;
    }
}

class_alias(FieldConfigurationUpdatedView::class, 'EzSystems\EzPlatformFormBuilder\View\FieldConfigurationUpdatedView');
