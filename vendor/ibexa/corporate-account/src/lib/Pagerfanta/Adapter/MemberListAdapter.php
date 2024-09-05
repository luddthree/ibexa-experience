<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Pagerfanta\Adapter;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\CorporateAccount\Service\MemberService;
use Ibexa\Contracts\CorporateAccount\Values\Company;
use Pagerfanta\Adapter\AdapterInterface;

/**
 * @implements \Pagerfanta\Adapter\AdapterInterface<\Ibexa\Contracts\CorporateAccount\Values\Member>
 */
class MemberListAdapter implements AdapterInterface
{
    private Criterion $filter;

    private Company $company;

    private MemberService $memberService;

    public function __construct(
        MemberService $memberService,
        Company $company,
        Criterion $filter
    ) {
        $this->memberService = $memberService;
        $this->company = $company;
        $this->filter = $filter;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function getNbResults(): int
    {
        return $this->memberService->countCompanyMembers($this->company, $this->filter);
    }

    /** @return array<\Ibexa\Contracts\CorporateAccount\Values\Member> */
    public function getSlice($offset, $length): array
    {
        return $this->memberService->getCompanyMembers(
            $this->company,
            $this->filter,
            [],
            $length,
            $offset
        );
    }
}
