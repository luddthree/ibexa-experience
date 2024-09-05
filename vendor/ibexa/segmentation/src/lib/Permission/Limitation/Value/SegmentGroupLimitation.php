<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\Permission\Limitation\Value;

use Ibexa\Contracts\Core\Repository\Values\User\Limitation;

final class SegmentGroupLimitation extends Limitation
{
    public const IDENTIFIER = 'SegmentGroup';

    public function getIdentifier(): string
    {
        return self::IDENTIFIER;
    }
}

class_alias(SegmentGroupLimitation::class, 'Ibexa\Platform\Segmentation\Permission\Limitation\Value\SegmentGroupLimitation');
