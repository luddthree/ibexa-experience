<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Content;

final class Matcher
{
    public const CONTENT_REMOTE_ID = 'content_remote_id';
    public const LOCATION_ID = 'location_id';
    public const PARENT_LOCATION_ID = 'parent_location_id';
    public const CONTENT_TYPE_IDENTIFIER = 'content_type_identifier';
}

/**
 * @deprecated Use Ibexa\Migration\ValueObject\Content\Matcher class instead
 */
class_alias(Matcher::class, 'Ibexa\Platform\Migration\ValueObject\Content\Match');

class_alias(Matcher::class, 'Ibexa\Platform\Migration\ValueObject\Content\Matcher');
