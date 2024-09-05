<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\REST\Value;

use ArrayIterator;
use Ibexa\Rest\Value as RestValue;
use IteratorAggregate;

/**
 * @internal
 */
final class CompanyList extends RestValue implements IteratorAggregate
{
    /** @var array<\Ibexa\CorporateAccount\REST\Value\Company> */
    private array $companyList;

    /**
     * @param array<\Ibexa\CorporateAccount\REST\Value\Company> $companyList
     */
    public function __construct(array $companyList)
    {
        $this->companyList = $companyList;
    }

    public function append(Company $company): void
    {
        $this->companyList[] = $company;
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->companyList);
    }
}
