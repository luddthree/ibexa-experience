<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\Value\Step\Action\Segment;

use Ibexa\Segmentation\Value\Step\Action\AbstractAssignUser;

final class AssignToUser extends AbstractAssignUser
{
    public const TYPE = 'assign_segment_to_user';

    public function getSupportedType(): string
    {
        return self::TYPE;
    }
}
