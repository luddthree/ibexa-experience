<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Segmentation\Serializer\Normalizer\Segment\Action;

use Ibexa\Bundle\Segmentation\Serializer\Normalizer\Segment\Action\AssignToUserActionNormalizer;
use Ibexa\Segmentation\Value\Step\Action\Segment\AssignToUser;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Bundle\Segmentation\Serializer\Normalizer\Segment\Action\AssignToUserActionNormalizer
 */
final class AssignToUserActionNormalizerTest extends TestCase
{
    /** @var \Ibexa\Bundle\Segmentation\Serializer\Normalizer\Segment\Action\AssignToUserActionNormalizer */
    private $normalizer;

    protected function setUp(): void
    {
        $this->normalizer = new AssignToUserActionNormalizer();
    }

    public function testSupportsAction(): void
    {
        self::assertFalse($this->normalizer->supportsNormalization((object)[]));
        self::assertTrue($this->normalizer->supportsNormalization(new AssignToUser(1)));
    }

    public function testNormalize(): void
    {
        $action = new AssignToUser(1, 'test&ibexa.co', 'test');

        $data = $this->normalizer->normalize($action);

        self::assertIsArray($data);
        self::assertArrayHasKey('action', $data);
        self::assertSame('assign_segment_to_user', $data['action']);
    }
}
