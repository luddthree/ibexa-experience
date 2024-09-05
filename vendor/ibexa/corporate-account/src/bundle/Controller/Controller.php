<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\Controller;

use Ibexa\Contracts\AdminUi\Controller\Controller as BaseController;
use Ibexa\CorporateAccount\Configuration\CorporateAccountConfiguration;

abstract class Controller extends BaseController
{
    protected CorporateAccountConfiguration $corporateAccount;

    public function __construct(
        CorporateAccountConfiguration $corporateAccount
    ) {
        $this->corporateAccount = $corporateAccount;
    }
}
