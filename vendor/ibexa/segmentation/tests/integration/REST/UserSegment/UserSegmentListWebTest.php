<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Platform\Segmentation\Tests\integration\REST\UserSegment;

use Ibexa\Platform\Segmentation\Tests\integration\REST\AbstractRestViewTestCase;

final class UserSegmentListWebTest extends AbstractRestViewTestCase
{
    protected static function getResourceType(): string
    {
        return 'UserSegmentList';
    }

    protected function getUri(): string
    {
        return self::BASE_URI . '/user/users/14/segments';
    }
}
