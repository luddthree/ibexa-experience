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
final class MemberList extends RestValue implements IteratorAggregate
{
    private Company $company;

    /** @var array<\Ibexa\CorporateAccount\REST\Value\Member> */
    private array $memberList;

    /**
     * @param array<\Ibexa\CorporateAccount\REST\Value\Member> $memberList
     */
    public function __construct(Company $company, array $memberList)
    {
        $this->company = $company;
        $this->memberList = $memberList;
    }

    public function append(Member $member): void
    {
        $this->memberList[] = $member;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->memberList);
    }
}
