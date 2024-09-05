<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Service\Account;

use Ibexa\Personalization\Value\Account;

/**
 * @internal
 */
interface AccountServiceInterface
{
    public function createAccount(string $name, string $template): Account;

    public function getAccount(): ?Account;
}
