<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Dashboard\Migration;

use Ibexa\Migration\ValueObject\Step\Action;

final class AssignDashboardToUser implements Action
{
    public const TYPE = 'assign_dashboard_to_user';

    private string $userLogin;

    public function __construct(string $userLogin)
    {
        $this->userLogin = $userLogin;
    }

    public function getValue(): string
    {
        return $this->userLogin;
    }

    public function getSupportedType(): string
    {
        return self::TYPE;
    }
}
