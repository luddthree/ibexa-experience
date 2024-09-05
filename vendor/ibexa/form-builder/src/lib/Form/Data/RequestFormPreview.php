<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Data;

use Ibexa\Contracts\FormBuilder\FieldType\Model\Form;

class RequestFormPreview
{
    /** @var \Ibexa\Contracts\FormBuilder\FieldType\Model\Form */
    protected $form;

    /**
     * @param \Ibexa\Contracts\FormBuilder\FieldType\Model\Form $form
     */
    public function __construct(
        Form $form = null
    ) {
        $this->form = $form;
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
}

class_alias(RequestFormPreview::class, 'EzSystems\EzPlatformFormBuilder\Form\Data\RequestFormPreview');
