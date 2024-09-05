<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\CorporateAccount\Event\Application\Workflow;

use Symfony\Component\Form\FormInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class MapApplicationWorkflowFormEvent extends Event
{
    private string $state;

    private ?FormInterface $form;

    /** @var array<string, mixed>|object|null */
    private $data;

    /**
     * @param string $state
     * @param array<string, mixed>|object|null $data
     * @param \Symfony\Component\Form\FormInterface|null $form
     */
    public function __construct(
        string $state,
        $data = null,
        ?FormInterface $form = null
    ) {
        $this->state = $state;
        $this->data = $data;
        $this->form = $form;
    }

    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @return array<string, mixed>|object|null
     */
    public function getData()
    {
        return $this->data;
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
