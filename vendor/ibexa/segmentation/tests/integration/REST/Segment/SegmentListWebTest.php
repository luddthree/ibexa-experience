<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Segmentation\REST\Segment;

use Ibexa\Platform\Segmentation\Tests\integration\REST\AbstractRestViewTestCase;

final class SegmentListWebTest extends AbstractRestViewTestCase
{
    protected static function getResourceType(): string
    {
        return 'SegmentList';
    }

    protected function getUri(): string
    {
        return self::BASE_URI . '/segment_groups/segment_test_group_foo/segments';
    }
}
