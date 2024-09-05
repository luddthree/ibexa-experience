<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Segmentation\Serializer\Denormalizer\Segment\Action;

use Ibexa\Bundle\Segmentation\Serializer\Denormalizer\Segment\Action\AssignToUserActionDenormalizer;
use Ibexa\Migration\ValueObject\Step\Action;
use Ibexa\Segmentation\Value\Step\Action\Segment\AssignToUser;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Bundle\Segmentation\Serializer\Denormalizer\Segment\Action\AssignToUserActionDenormalizer
 */
final class AssignToUserActionDenormalizerTest extends TestCase
{
    /** @var \Ibexa\Bundle\Segmentation\Serializer\Denormalizer\Segment\Action\AssignToUserActionDenormalizer */
    private $denormalizer;

    protected function setUp(): void
    {
        $this->denormalizer = new AssignToUserActionDenormalizer();
    }

    public function testSupportActionName(): void
    {
        self::assertTrue($this->denormalizer->supportsDenormalization([
            'action' => 'assign_segment_to_user',
        ], Action::class));
    }

    public function testDenormalize(): void
    {
        $data = [
            'id' => 1,
        ];

        $action = $this->denormalizer->denormalize($data, Action::class);

        self::assertInstanceOf(AssignToUser::class, $action);
        self::assertSame($data['id'], $action->getId());
    }
}
