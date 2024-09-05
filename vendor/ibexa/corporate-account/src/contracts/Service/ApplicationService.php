<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\CorporateAccount\Service;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\CorporateAccount\Values\Application;
use Ibexa\Contracts\CorporateAccount\Values\ApplicationCreateStruct;
use Ibexa\Contracts\CorporateAccount\Values\ApplicationState;
use Ibexa\Contracts\CorporateAccount\Values\ApplicationUpdateStruct;

interface ApplicationService
{
    public function getApplication(int $id): Application;

    public function getApplicationsCount(
        ?Criterion $filter = null
    ): int;

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause[] $sortClauses
     *
     * @return array<\Ibexa\Contracts\CorporateAccount\Values\Application>
     */
    public function getApplications(
        ?Criterion $filter = null,
        array $sortClauses = [],
        ?int $limit = 25,
        int $offset = 0
    ): array;

    /** @param string[]|null $fieldIdentifiersToValidate */
    public function createApplication(
        ApplicationCreateStruct $applicationCreateStruct,
        ?array $fieldIdentifiersToValidate = null
    ): Application;

    /** @param string[]|null $fieldIdentifiersToValidate */
    public function updateApplication(
        Application $application,
        ApplicationUpdateStruct $applicationUpdateStruct,
        ?array $fieldIdentifiersToValidate = null
    ): Application;

    public function deleteApplication(Application $application): void;

    public function newApplicationCreateStruct(): ApplicationCreateStruct;

    public function newApplicationUpdateStruct(): ApplicationUpdateStruct;

    public function getApplicationState(Application $application): ApplicationState;

    /**
     * @param \Ibexa\Contracts\CorporateAccount\Values\Application[] $applications
     *
     * @return \Ibexa\Contracts\CorporateAccount\Values\ApplicationState[]
     */
    public function getApplicationsStates(array $applications): array;
}
