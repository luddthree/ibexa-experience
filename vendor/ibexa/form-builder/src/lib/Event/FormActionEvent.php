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

class FormActionEvent extends Event
{
    private ContentView $contentView;

    private Form $form;

    private string $action;

    /** @var mixed */
    private $data;

    /**
     * @param mixed $data
     */
    public function __construct(
        ContentView $contentView,
        Form $form,
        string $action,
        $data
    ) {
        $this->contentView = $contentView;
        $this->form = $form;
        $this->action = $action;
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
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @param string $action
     */
    public function setAction(string $action): void
    {
        $this->action = $action;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data): void
    {
        $this->data = $data;
    }
}

class_alias(FormActionEvent::class, 'EzSystems\EzPlatformFormBuilder\Event\FormActionEvent');
