<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\Security;

use Ibexa\Core\MVC\Symfony\Security\UserInterface;
use Symfony\Component\Security\Core\Exception\AccountStatusException;

final class UserIsNotMemberException extends AccountStatusException
{
    public function __construct(UserInterface $user, string $memberContentType, int $code = 0, \Throwable $previous = null)
    {
        parent::__construct(
            sprintf(
                'User is not a %s content type',
                $memberContentType
            ),
            $code,
            $previous
        );
        $this->setUser($user);
    }
}
