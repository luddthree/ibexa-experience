<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Segmentation\REST\Segment;

use Ibexa\Platform\Segmentation\Tests\integration\REST\AbstractRestViewTestCase;

final class SegmentViewWebTest extends AbstractRestViewTestCase
{
    protected static function getResourceType(): string
    {
        return 'Segment';
    }

    protected function getUri(): string
    {
        return self::BASE_URI . '/segments/segment_test_foo';
    }
}
