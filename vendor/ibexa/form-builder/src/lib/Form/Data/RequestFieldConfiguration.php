<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Data;

use Ibexa\Contracts\FormBuilder\FieldType\Model\Form;

class RequestFieldConfiguration
{
    /** @var \Ibexa\Contracts\FormBuilder\FieldType\Model\Form */
    protected $form;

    /** @var string */
    private $fieldId;

    /**
     * @param \Ibexa\Contracts\FormBuilder\FieldType\Model\Form $form
     * @param string $fieldId
     */
    public function __construct(
        Form $form = null,
        string $fieldId = null
    ) {
        $this->form = $form;
        $this->fieldId = $fieldId;
    }

    /**
     * @return \Ibexa\Contracts\FormBuilder\FieldType\Model\Form
     */
    public function getForm(): ?Form
    {
        return $this->form;
    }

    /**
     * @param \Ibexa\Contracts\FormBuilder\FieldType\Model\Form $form
     */
    public function setForm(?Form $form): void
    {
        $this->form = $form;
    }

    /**
     * @return string|null
     */
    public function getFieldId(): ?string
    {
        return $this->fieldId;
    }

    /**
     * @param string|null $fieldId
     */
    public function setFieldId(?string $fieldId): void
    {
        $this->fieldId = $fieldId;
    }
}

class_alias(RequestFieldConfiguration::class, 'EzSystems\EzPlatformFormBuilder\Form\Data\RequestFieldConfiguration');
