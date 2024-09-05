<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Exception;

use Ibexa\Contracts\ProductCatalog\Permission\Policy\PolicyInterface;
use Ibexa\Core\Base\Exceptions\UnauthorizedException as BaseUnauthorizedException;

final class UnauthorizedException extends BaseUnauthorizedException
{
    public function __construct(PolicyInterface $policy)
    {
        parent::__construct(
            $policy->getModule(),
            $policy->getFunction(),
        );
    }
}
