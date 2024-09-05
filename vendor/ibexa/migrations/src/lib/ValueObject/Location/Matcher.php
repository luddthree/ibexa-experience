<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Location;

use Ibexa\Migration\ValueObject\AbstractMatcher;

final class Matcher extends AbstractMatcher
{
    public const LOCATION_REMOTE_ID = 'location_remote_id';
    public const LOCATION_ID = 'location_id';

    protected function getSupportedFields(): array
    {
        return [
            self::LOCATION_REMOTE_ID => self::LOCATION_REMOTE_ID,
            self::LOCATION_ID => self::LOCATION_ID,
        ];
    }
}

/**
 * @deprecated Use Ibexa\Migration\ValueObject\Location\Matcher class instead
 */
class_alias(Matcher::class, 'Ibexa\Platform\Migration\ValueObject\Location\Match');

class_alias(Matcher::class, 'Ibexa\Platform\Migration\ValueObject\Location\Matcher');
