<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Cdp\Export\User;

use Ibexa\Contracts\Cdp\Export\User\AbstractUserItemProcessor;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\CorporateAccount\Service\CompanyService;
use Ibexa\Contracts\CorporateAccount\Service\MemberService;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;

final class CompanyInfoUserItemProcessor extends AbstractUserItemProcessor
{
    private MemberService $memberService;

    private CompanyService $companyService;

    public function __construct(
        MemberService $memberService,
        CompanyService $companyService,
        string $userFieldTypeIdentifier
    ) {
        $this->memberService = $memberService;
        $this->companyService = $companyService;

        parent::__construct($userFieldTypeIdentifier);
    }

    protected function doProcess(array $processedItemData, Content $userContent): array
    {
        $userField = $this->getUserField($userContent);

        if (null === $userField) {
            throw new InvalidArgumentException('$item', 'Item does not contain user field');
        }

        $memberAssignmentData = [];
        foreach ($this->memberService->getMemberAssignmentsByMemberId($userContent->id) as $memberAssignment) {
            $company = $this->companyService->getCompany($memberAssignment->getCompanyId());
            $memberAssignmentData[] = [
                'company_id' => $company->getId(),
                'company_name' => $company->getName(),
            ];
        }

        return array_merge(
            $processedItemData,
            [
                'companies' => $memberAssignmentData,
            ]
        );
    }
}
