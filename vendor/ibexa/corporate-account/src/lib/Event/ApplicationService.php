<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Event;

use Ibexa\Contracts\CorporateAccount\Event\Application\BeforeCreateApplicationEvent;
use Ibexa\Contracts\CorporateAccount\Event\Application\BeforeDeleteApplicationEvent;
use Ibexa\Contracts\CorporateAccount\Event\Application\BeforeUpdateApplicationEvent;
use Ibexa\Contracts\CorporateAccount\Event\Application\CreateApplicationEvent;
use Ibexa\Contracts\CorporateAccount\Event\Application\DeleteApplicationEvent;
use Ibexa\Contracts\CorporateAccount\Event\Application\UpdateApplicationEvent;
use Ibexa\Contracts\CorporateAccount\Service\ApplicationService as ApplicationServiceInterface;
use Ibexa\Contracts\CorporateAccount\Values\Application;
use Ibexa\Contracts\CorporateAccount\Values\ApplicationCreateStruct;
use Ibexa\Contracts\CorporateAccount\Values\ApplicationUpdateStruct;
use Ibexa\CorporateAccount\Decorator\ApplicationService as ApplicationServiceDecorator;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class ApplicationService extends ApplicationServiceDecorator
{
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        ApplicationServiceInterface $innerService,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct($innerService);

        $this->eventDispatcher = $eventDispatcher;
    }

    public function createApplication(
        ApplicationCreateStruct $applicationCreateStruct,
        ?array $fieldIdentifiersToValidate = null
    ): Application {
        $parameters = [$applicationCreateStruct, $fieldIdentifiersToValidate];

        $beforeEvent = new BeforeCreateApplicationEvent(...$parameters);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getApplication();
        }

        $application = $beforeEvent->hasApplication()
            ? $beforeEvent->getApplication()
            : $this->innerService->createApplication(...$parameters);

        $this->eventDispatcher->dispatch(
            new CreateApplicationEvent($application, ...$parameters)
        );

        return $application;
    }

    public function updateApplication(
        Application $application,
        ApplicationUpdateStruct $applicationUpdateStruct,
        ?array $fieldIdentifiersToValidate = null
    ): Application {
        $parameters = [$application, $applicationUpdateStruct, $fieldIdentifiersToValidate];

        $beforeEvent = new BeforeUpdateApplicationEvent(...$parameters);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getUpdatedApplication();
        }

        $updatedApplication = $beforeEvent->hasUpdatedApplication()
            ? $beforeEvent->getUpdatedApplication()
            : $this->innerService->updateApplication(...$parameters);

        $this->eventDispatcher->dispatch(
            new UpdateApplicationEvent($updatedApplication, ...$parameters)
        );

        return $updatedApplication;
    }

    public function deleteApplication(Application $application): void
    {
        $parameters = [$application];

        $beforeEvent = new BeforeDeleteApplicationEvent(...$parameters);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->deleteApplication(...$parameters);

        $this->eventDispatcher->dispatch(
            new DeleteApplicationEvent(...$parameters)
        );
    }
}
