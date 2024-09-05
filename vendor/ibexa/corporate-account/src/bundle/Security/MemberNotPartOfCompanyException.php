<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\Security;

use Ibexa\Core\MVC\Symfony\Security\UserInterface;
use Symfony\Component\Security\Core\Exception\AccountStatusException;

final class MemberNotPartOfCompanyException extends AccountStatusException
{
    public function __construct(
        UserInterface $user,
        string $message = 'Member is not a part of any company',
        int $code = 0,
        \Throwable $previous = null
    ) {
        parent::__construct(
            $message,
            $code,
            $previous
        );
        $this->setUser($user);
    }
}
