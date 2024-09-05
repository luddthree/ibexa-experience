<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Pagerfanta\Adapter;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\CorporateAccount\Service\CompanyService;
use Pagerfanta\Adapter\AdapterInterface;

final class CompanyListAdapter implements AdapterInterface
{
    private CompanyService $companyService;

    private ?Criterion $filter;

    public function __construct(
        CompanyService $companyService,
        ?Criterion $filter = null
    ) {
        $this->companyService = $companyService;
        $this->filter = $filter;
    }

    public function getNbResults(): int
    {
        return $this->companyService->getCompaniesCount($this->filter);
    }

    /** @return array<\Ibexa\Contracts\CorporateAccount\Values\Company> */
    public function getSlice($offset, $length): array
    {
        return $this->companyService->getCompanies(
            $this->filter,
            [],
            $length,
            $offset
        );
    }
}
