<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Event;

use Ibexa\Contracts\FormBuilder\FieldType\Model\Form;
use Ibexa\Core\MVC\Symfony\View\ContentView;
use Symfony\Contracts\EventDispatcher\Event;

class FormSubmitEvent extends Event
{
    /** @var \Ibexa\Core\MVC\Symfony\View\ContentView */
    private $contentView;

    /** @var \Ibexa\Contracts\FormBuilder\FieldType\Model\Form */
    private $form;

    /** @var array */
    private $data;

    /**
     * @param \Ibexa\Core\MVC\Symfony\View\ContentView $contentView
     * @param \Ibexa\Contracts\FormBuilder\FieldType\Model\Form $form
     * @param array $data
     */
    public function __construct(
        ContentView $contentView,
        Form $form,
        array $data
    ) {
        $this->contentView = $contentView;
        $this->form = $form;
        $this->data = $data;
    }

    /**
     * @return \Ibexa\Core\MVC\Symfony\View\ContentView
     */
    public function getContentView(): ContentView
    {
        return $this->contentView;
    }

    /**
     * @param \Ibexa\Core\MVC\Symfony\View\ContentView $contentView
     */
    public function setContentView(ContentView $contentView): void
    {
        $this->contentView = $contentView;
    }

    /**
     * @return \Ibexa\Contracts\FormBuilder\FieldType\Model\Form
     */
    public function getForm(): Form
    {
        return $this->form;
    }

    /**
     * @param \Ibexa\Contracts\FormBuilder\FieldType\Model\Form $form
     */
    public function setForm(Form $form): void
    {
        $this->form = $form;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }
}

class_alias(FormSubmitEvent::class, 'EzSystems\EzPlatformFormBuilder\Event\FormSubmitEvent');
