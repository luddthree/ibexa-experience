<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Decorator;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\CorporateAccount\Service\ApplicationService as ApplicationServiceInterface;
use Ibexa\Contracts\CorporateAccount\Values\Application;
use Ibexa\Contracts\CorporateAccount\Values\ApplicationCreateStruct;
use Ibexa\Contracts\CorporateAccount\Values\ApplicationState;
use Ibexa\Contracts\CorporateAccount\Values\ApplicationUpdateStruct;

abstract class ApplicationService implements ApplicationServiceInterface
{
    protected ApplicationServiceInterface $innerService;

    public function __construct(
        ApplicationServiceInterface $innerService
    ) {
        $this->innerService = $innerService;
    }

    public function getApplication(int $id): Application
    {
        return $this->innerService->getApplication($id);
    }

    public function getApplicationsCount(?Criterion $filter = null): int
    {
        return $this->innerService->getApplicationsCount($filter);
    }

    public function getApplications(
        ?Criterion $filter = null,
        array $sortClauses = [],
        ?int $limit = 25,
        int $offset = 0
    ): array {
        return $this->innerService->getApplications(
            $filter,
            $sortClauses,
            $limit,
            $offset
        );
    }

    public function createApplication(
        ApplicationCreateStruct $applicationCreateStruct,
        ?array $fieldIdentifiersToValidate = null
    ): Application {
        return $this->innerService->createApplication(
            $applicationCreateStruct,
            $fieldIdentifiersToValidate
        );
    }

    public function updateApplication(
        Application $application,
        ApplicationUpdateStruct $applicationUpdateStruct,
        ?array $fieldIdentifiersToValidate = null
    ): Application {
        return $this->innerService->updateApplication(
            $application,
            $applicationUpdateStruct,
            $fieldIdentifiersToValidate
        );
    }

    public function deleteApplication(Application $application): void
    {
        $this->innerService->deleteApplication($application);
    }

    public function newApplicationCreateStruct(): ApplicationCreateStruct
    {
        return $this->innerService->newApplicationCreateStruct();
    }

    public function newApplicationUpdateStruct(): ApplicationUpdateStruct
    {
        return $this->innerService->newApplicationUpdateStruct();
    }

    public function getApplicationState(Application $application): ApplicationState
    {
        return $this->innerService->getApplicationState($application);
    }

    public function getApplicationsStates(array $applications): array
    {
        return $this->innerService->getApplicationsStates($applications);
    }
}
