<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Pagerfanta\Adapter;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\CorporateAccount\Service\ApplicationService;
use Pagerfanta\Adapter\AdapterInterface;

final class ApplicationListAdapter implements AdapterInterface
{
    private ApplicationService $applicationService;

    private ?Criterion $filter;

    public function __construct(
        ApplicationService $applicationService,
        ?Criterion $filter = null
    ) {
        $this->applicationService = $applicationService;
        $this->filter = $filter;
    }

    public function getNbResults(): int
    {
        return $this->applicationService->getApplicationsCount($this->filter);
    }

    /** @return array<\Ibexa\Contracts\CorporateAccount\Values\Application> */
    public function getSlice($offset, $length): array
    {
        return $this->applicationService->getApplications(
            $this->filter,
            [],
            $length,
            $offset
        );
    }
}
