<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Token;

use Ibexa\Personalization\Value\Item\Action;

final class Token
{
    public const TYPE = 'personalization';

    public const IDENTIFIER_EXPORT = Action::ACTION_EXPORT;
    public const IDENTIFIER_UPDATE = Action::ACTION_UPDATE;
}
