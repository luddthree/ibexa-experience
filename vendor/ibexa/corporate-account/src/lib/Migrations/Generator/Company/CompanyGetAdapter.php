<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Migrations\Generator\Company;

use ArrayIterator;
use Ibexa\Contracts\Core\Repository\Iterator\BatchIteratorAdapter;
use Ibexa\Contracts\CorporateAccount\Service\CompanyService;
use Iterator;

final class CompanyGetAdapter implements BatchIteratorAdapter
{
    private CompanyService $companyService;

    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    public function fetch(int $offset, int $limit): Iterator
    {
        return new ArrayIterator($this->companyService->getCompanies(null, [], $limit, $offset));
    }
}
