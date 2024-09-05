<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\CorporateAccount\Exception;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Throwable;

final class CustomerPortalMainPageNotFoundException extends NotFoundException
{
    public function __construct(
        Location $customerPortalLocation,
        int $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct(
            sprintf(
                'No pages found for %s Customer Portal',
                $customerPortalLocation->getContent()->getName()
            ),
            $code,
            $previous
        );
    }
}
