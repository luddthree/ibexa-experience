<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\Controller;

use Ibexa\AdminUi\Exception\InvalidArgumentException;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\Core\Repository\Exceptions\Exception;
use Ibexa\Contracts\CorporateAccount\Event\Application\Workflow\ApplicationWorkflowFormEvent;
use Ibexa\Contracts\CorporateAccount\Event\Application\Workflow\MapApplicationWorkflowFormEvent;
use Ibexa\Contracts\CorporateAccount\Service\ApplicationService;
use Ibexa\CorporateAccount\Configuration\CorporateAccountConfiguration;
use Ibexa\CorporateAccount\Event\ApplicationWorkflowEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ApplicationWorkflowController extends Controller
{
    private ApplicationService $applicationService;

    private EventDispatcherInterface $eventDispatcher;

    private TranslatableNotificationHandlerInterface $notificationHandler;

    public function __construct(
        CorporateAccountConfiguration $corporateAccount,
        ApplicationService $applicationService,
        EventDispatcherInterface $eventDispatcher,
        TranslatableNotificationHandlerInterface $notificationHandler
    ) {
        parent::__construct($corporateAccount);

        $this->applicationService = $applicationService;
        $this->eventDispatcher = $eventDispatcher;
        $this->notificationHandler = $notificationHandler;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function dispatchAction(Request $request, string $state): Response
    {
        /** @var \Ibexa\Contracts\CorporateAccount\Event\Application\Workflow\MapApplicationWorkflowFormEvent $event */
        $event = $this->eventDispatcher->dispatch(
            new MapApplicationWorkflowFormEvent($state)
        );

        $form = $event->getForm();

        if (null === $form) {
            throw new InvalidArgumentException('state', 'Could not map workflow form.');
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $application = $this->applicationService->getApplication((int)$data['application']);
            $applicationState = $this->applicationService->getApplicationState($application);
            $event = new ApplicationWorkflowFormEvent(
                $application,
                $applicationState,
                $form,
                $data,
                $applicationState->getState(),
                $state
            );

            try {
                $this->eventDispatcher->dispatch($event, ApplicationWorkflowEvents::APPLICATION_WORKFLOW);
                $this->eventDispatcher->dispatch($event, ApplicationWorkflowEvents::getStateEvent($state));
            } catch (Exception $exception) {
                $this->notificationHandler->error($exception->getMessage());
            }

            if (null !== $event->getResponse()) {
                return $event->getResponse();
            }

            return $this->redirectToRoute('ibexa.corporate_account.application.details', [
                'applicationId' => $application->getId(),
            ]);
        }

        throw new InvalidArgumentException('state', 'Could not handle state form.');
    }
}
