<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Step\Action\Content;

use Ibexa\Migration\ValueObject\Step\Action;

final class Hide implements Action
{
    public const TYPE = 'hide';

    public function getValue(): void
    {
    }

    public function getSupportedType(): string
    {
        return self::TYPE;
    }
}
