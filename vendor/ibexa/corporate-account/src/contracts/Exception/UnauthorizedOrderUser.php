<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\CorporateAccount\Exception;

use Ibexa\Contracts\Core\Repository\Exceptions\BadStateException;

final class UnauthorizedOrderUser extends BadStateException
{
    public function __construct(int $orderId, int $companyId)
    {
        parent::__construct(
            sprintf('User outside company of id %d created order of id %d', $companyId, $orderId)
        );
    }
}
