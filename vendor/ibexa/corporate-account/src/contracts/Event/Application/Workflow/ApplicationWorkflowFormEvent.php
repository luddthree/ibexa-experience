<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\CorporateAccount\Event\Application\Workflow;

use Ibexa\Contracts\CorporateAccount\Values\Application;
use Ibexa\Contracts\CorporateAccount\Values\ApplicationState;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\Event;

final class ApplicationWorkflowFormEvent extends Event
{
    private Application $application;

    private ApplicationState $applicationState;

    private FormInterface $form;

    /** @var array<string, mixed>|null */
    private ?array $data;

    private ?Response $response;

    private string $previousState;

    private string $nextState;

    /**
     * @param array<string, mixed>|null $data
     */
    public function __construct(
        Application $application,
        ApplicationState $applicationState,
        FormInterface $form,
        ?array $data,
        string $previousState,
        string $nextState,
        ?Response $response = null
    ) {
        $this->application = $application;
        $this->applicationState = $applicationState;
        $this->form = $form;
        $this->data = $data;
        $this->response = $response;
        $this->previousState = $previousState;
        $this->nextState = $nextState;
    }

    public function getApplication(): Application
    {
        return $this->application;
    }

    public function getApplicationState(): ApplicationState
    {
        return $this->applicationState;
    }

    public function getForm(): FormInterface
    {
        return $this->form;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getData(): ?array
    {
        return $this->data;
    }

    public function getResponse(): ?Response
    {
        return $this->response;
    }

    public function setResponse(?Response $response): void
    {
        $this->response = $response;
    }

    public function getPreviousState(): string
    {
        return $this->previousState;
    }

    public function getNextState(): string
    {
        return $this->nextState;
    }
}
