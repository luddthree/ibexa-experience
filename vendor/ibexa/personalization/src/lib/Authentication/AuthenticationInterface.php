<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Authentication;

use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
interface AuthenticationInterface
{
    /**
     * @param \Ibexa\Personalization\Value\Item\Action::ACTION_* $action
     */
    public function authenticate(Request $request, string $action): bool;
}
