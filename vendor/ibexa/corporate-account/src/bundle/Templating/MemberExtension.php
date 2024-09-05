<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\Templating;

use Ibexa\Contracts\CorporateAccount\Service\CompanyService;
use Ibexa\Contracts\CorporateAccount\Service\MemberService;
use Ibexa\Contracts\CorporateAccount\Values\Member;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @internal
 */
final class MemberExtension extends AbstractExtension
{
    private MemberService $memberService;

    private CompanyService $companyService;

    public function __construct(
        MemberService $memberService,
        CompanyService $companyService
    ) {
        $this->memberService = $memberService;
        $this->companyService = $companyService;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'ibexa_get_member',
                [$this, 'getMemberByUserId'],
                [
                    'is_safe' => ['html'],
                ]
            ),
        ];
    }

    public function getMemberByUserId(int $userId, int $companyId): Member
    {
        return $this->memberService->getMember(
            $userId,
            $this->companyService->getCompany($companyId)
        );
    }
}
