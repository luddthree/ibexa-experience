<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\REST\Value;

use Ibexa\Contracts\CorporateAccount\Values\CompanyUpdateStruct as APICompanyUpdateStruct;
use Ibexa\Rest\Value as RestValue;

/**
 * @internal
 */
final class CompanyUpdateStruct extends RestValue
{
    public APICompanyUpdateStruct $companyUpdateStruct;

    public function __construct(APICompanyUpdateStruct $companyUpdateStruct)
    {
        $this->companyUpdateStruct = $companyUpdateStruct;
    }
}
