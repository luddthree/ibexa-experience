<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Calendar;

use DateTime;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\User\User;

class BlockRevealEvent extends BlockVisibilityEvent
{
    public function __construct(
        BlockRevealEventType $type,
        string $id,
        DateTime $date,
        Language $language,
        User $user,
        Content $content,
        string $blockName,
        string $blockType
    ) {
        parent::__construct(
            $type,
            $id,
            $date,
            $language,
            $user,
            $content,
            $blockName,
            $blockType
        );
    }
}

class_alias(BlockRevealEvent::class, 'EzSystems\EzPlatformPageFieldType\Calendar\BlockRevealEvent');
