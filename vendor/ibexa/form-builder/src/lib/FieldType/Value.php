<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\FieldType;

use Ibexa\Contracts\FormBuilder\FieldType\Model\Form;
use Ibexa\Core\FieldType\Value as BaseValue;
use Symfony\Component\Form\FormInterface;

class Value extends BaseValue
{
    /** @var \Ibexa\Contracts\FormBuilder\FieldType\Model\Form|null */
    private $formValue;

    /** @var \Ibexa\Contracts\FormBuilder\FieldType\Model\Form|\Symfony\Component\Form\FormInterface|null */
    private $form;

    /**
     * @param \Ibexa\Contracts\FormBuilder\FieldType\Model\Form|null $formValue
     * @param \Symfony\Component\Form\FormInterface|null $form
     */
    public function __construct(
        ?Form $formValue = null,
        ?FormInterface $form = null
    ) {
        parent::__construct();

        $this->formValue = $formValue;
        $this->form = $form;
    }

    /**
     * Returns a string representation of the field value.
     *
     * @return string
     */
    public function __toString()
    {
        return '';
    }

    public function getFormValue(): ?Form
    {
        return $this->formValue;
    }

    public function setFormValue(?Form $formValue): void
    {
        $this->formValue = $formValue;
    }

    public function getForm(): ?FormInterface
    {
        return $this->form;
    }

    public function setForm(?FormInterface $form): void
    {
        $this->form = $form;
    }
}

class_alias(Value::class, 'EzSystems\EzPlatformFormBuilder\FieldType\Value');
