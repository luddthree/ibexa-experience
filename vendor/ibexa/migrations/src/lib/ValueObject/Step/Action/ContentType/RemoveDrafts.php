<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Step\Action\ContentType;

use Ibexa\Migration\ValueObject\Step\Action;

final class RemoveDrafts implements Action
{
    public const TYPE = 'remove_drafts';

    /**
     * @phpstan-return null
     */
    public function getValue()
    {
        return null;
    }

    public function getSupportedType(): string
    {
        return self::TYPE;
    }
}

class_alias(RemoveDrafts::class, 'Ibexa\Platform\Migration\ValueObject\Step\Action\ContentType\RemoveDrafts');
