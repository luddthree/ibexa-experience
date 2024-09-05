<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\View;

use Ibexa\Core\MVC\Symfony\View\BaseView;
use Symfony\Component\Form\FormView;

class FieldConfigurationView extends BaseView
{
    /** @var \Symfony\Component\Form\FormView */
    protected $form;

    /**
     * @param string $parameter
     * @param mixed $value
     */
    public function setParameter(string $parameter, $value): void
    {
        $this->parameters[$parameter] = $value;
    }

    /**
     * @return \Symfony\Component\Form\FormView
     */
    public function getForm(): FormView
    {
        return $this->form;
    }

    /**
     * @param \Symfony\Component\Form\FormView $form
     *
     * @return self
     */
    public function setForm(FormView $form): self
    {
        $this->form = $form;

        return $this;
    }
}

class_alias(FieldConfigurationView::class, 'EzSystems\EzPlatformFormBuilder\View\FieldConfigurationView');
