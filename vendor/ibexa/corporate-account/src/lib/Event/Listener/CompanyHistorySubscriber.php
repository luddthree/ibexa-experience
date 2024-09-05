<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Event\Listener;

use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\CorporateAccount\Event\Application\CreateApplicationEvent;
use Ibexa\Contracts\CorporateAccount\Event\Application\Workflow\ApplicationWorkflowFormEvent;
use Ibexa\Contracts\CorporateAccount\Service\ApplicationService;
use Ibexa\Contracts\CorporateAccount\Values\ApplicationState;
use Ibexa\CorporateAccount\Event\ApplicationWorkflowEvents;
use Ibexa\CorporateAccount\Persistence\Legacy\CompanyHistory\HandlerInterface;
use Ibexa\CorporateAccount\Persistence\Values\CompanyHistoryCreateStruct;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class CompanyHistorySubscriber implements EventSubscriberInterface
{
    private const COMPANY_HISTORY_APPLICATION_WORKFLOW = 'application_workflow';

    private HandlerInterface $handler;

    private ApplicationService $applicationService;

    private PermissionResolver $permissionResolver;

    public function __construct(
        HandlerInterface $handler,
        ApplicationService $applicationService,
        PermissionResolver $permissionResolver
    ) {
        $this->handler = $handler;
        $this->applicationService = $applicationService;
        $this->permissionResolver = $permissionResolver;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ApplicationWorkflowEvents::APPLICATION_WORKFLOW => ['onApplicationWorkflow', -50],
            CreateApplicationEvent::class => ['onApplicationCreate', -50],
        ];
    }

    public function onApplicationCreate(CreateApplicationEvent $event): void
    {
        $applicationState = $this->applicationService->getApplicationState(
            $event->getApplication()
        );

        $this->createCompanyHistoryFromApplicationState(
            $applicationState,
            $applicationState->getState()
        );
    }

    public function onApplicationWorkflow(ApplicationWorkflowFormEvent $event): void
    {
        $this->createCompanyHistoryFromApplicationState(
            $this->applicationService->getApplicationState(
                $event->getApplication()
            ),
            $event->getNextState(),
            $event->getPreviousState(),
            $event->getData() ?? []
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    private function createCompanyHistoryFromApplicationState(
        ApplicationState $applicationState,
        string $nextState,
        ?string $previousState = null,
        array $data = []
    ): void {
        $this->handler->create(
            new CompanyHistoryCreateStruct(
                $applicationState->getApplicationId(),
                $applicationState->getCompanyId(),
                $this->permissionResolver->getCurrentUserReference()->getUserId(),
                self::COMPANY_HISTORY_APPLICATION_WORKFLOW,
                array_merge([
                    'to' => $nextState,
                    'from' => $previousState ?? null,
                ], $data)
            )
        );
    }
}
