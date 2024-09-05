<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\REST\Value;

use Ibexa\Contracts\CorporateAccount\Values\CompanyCreateStruct as APICompanyCreateStruct;
use Ibexa\Rest\Value as RestValue;

/**
 * @internal
 */
final class CompanyCreateStruct extends RestValue
{
    public APICompanyCreateStruct $companyCreateStruct;

    public function __construct(APICompanyCreateStruct $companyCreateStruct)
    {
        $this->companyCreateStruct = $companyCreateStruct;
    }
}
