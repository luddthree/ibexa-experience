<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\ParamConverter;

use Ibexa\Contracts\CorporateAccount\Service\CompanyService;
use Ibexa\Contracts\CorporateAccount\Values\Company;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

class CompanyParamConverter implements ParamConverterInterface
{
    public const PARAMETER_COMPANY = 'company';
    public const PARAMETER_COMPANY_ID = 'companyId';

    private CompanyService $companyService;

    public function __construct(
        CompanyService $companyService
    ) {
        $this->companyService = $companyService;
    }

    public function apply(
        Request $request,
        ParamConverter $configuration
    ): bool {
        $companyId = $request->get(self::PARAMETER_COMPANY_ID);

        if (null === $companyId) {
            return false;
        }

        $company = $this->companyService->getCompany((int)$companyId);

        $request->attributes->set($configuration->getName(), $company);

        return true;
    }

    public function supports(ParamConverter $configuration): bool
    {
        return Company::class === $configuration->getClass()
            && self::PARAMETER_COMPANY === $configuration->getName();
    }
}
