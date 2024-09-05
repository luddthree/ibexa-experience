<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\CorporateAccount\Exception;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\CorporateAccount\Values\Member;
use Throwable;

final class CustomerPortalNotFoundException extends NotFoundException
{
    public function __construct(
        Member $member,
        int $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct(
            sprintf(
                'No Customer Portal found for %s Member ID, in %s Company',
                $member->getName(),
                $member->getCompany()->getName()
            ),
            $code,
            $previous
        );
    }
}
