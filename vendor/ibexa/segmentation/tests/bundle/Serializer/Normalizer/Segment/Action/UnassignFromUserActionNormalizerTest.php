<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Segmentation\Serializer\Normalizer\Segment\Action;

use Ibexa\Bundle\Segmentation\Serializer\Normalizer\Segment\Action\UnassignFromUserActionNormalizer;
use Ibexa\Segmentation\Value\Step\Action\Segment\UnassignFromUser;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Bundle\Segmentation\Serializer\Normalizer\Segment\Action\UnassignFromUserActionNormalizer
 */
final class UnassignFromUserActionNormalizerTest extends TestCase
{
    /** @var \Ibexa\Bundle\Segmentation\Serializer\Normalizer\Segment\Action\UnassignFromUserActionNormalizer */
    private $normalizer;

    protected function setUp(): void
    {
        $this->normalizer = new UnassignFromUserActionNormalizer();
    }

    public function testSupportsAction(): void
    {
        self::assertFalse($this->normalizer->supportsNormalization((object)[]));
        self::assertTrue($this->normalizer->supportsNormalization(new UnassignFromUser(1)));
    }

    public function testNormalize(): void
    {
        $action = new UnassignFromUser(1, 'test&ibexa.co', 'test');

        $data = $this->normalizer->normalize($action);

        self::assertIsArray($data);
        self::assertArrayHasKey('action', $data);
        self::assertSame('unassign_segment_from_user', $data['action']);
    }
}
