<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\CorporateAccount\Values\Limitation;

use Ibexa\Contracts\Core\Repository\Values\User\Limitation;

final class ApplicationStateLimitation extends Limitation
{
    public const IDENTIFIER = 'ApplicationState';

    public function getIdentifier(): string
    {
        return self::IDENTIFIER;
    }
}
