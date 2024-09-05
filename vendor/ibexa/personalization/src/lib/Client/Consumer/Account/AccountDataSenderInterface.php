<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Client\Consumer\Account;

use Psr\Http\Message\ResponseInterface;

/**
 * @internal
 */
interface AccountDataSenderInterface
{
    public function createAccount(
        string $installationKey,
        string $name,
        string $template
    ): ResponseInterface;
}
