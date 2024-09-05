<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Segmentation\Serializer\Normalizer\Segment\Action;

use Ibexa\Platform\Segmentation\Tests\integration\AbstractSerializerTestCase;
use Ibexa\Segmentation\Value\Step\Action\Segment\AssignToUser;

/**
 * @covers \Ibexa\Bundle\Segmentation\Serializer\Normalizer\Segment\Action\AssignToUserActionNormalizer
 */
final class AssignToUserActionNormalizerTest extends AbstractSerializerTestCase
{
    /**
     * @dataProvider provideDataForSerialization
     */
    public function testSerialization(AssignToUser $action, string $expected): void
    {
        $yaml = $this->serializer->serialize($action, 'yaml');

        self::assertSame($expected, $yaml);
    }

    /**
     * @return iterable<array{\Ibexa\Segmentation\Value\Step\Action\Segment\AssignToUser, non-empty-string}>
     */
    public function provideDataForSerialization(): iterable
    {
        yield [
            new AssignToUser(42),
            <<<YAML
            action: assign_segment_to_user
            id: 42

            YAML,
        ];

        yield [
            new AssignToUser(null, 'foo@foo.io'),
            <<<YAML
            action: assign_segment_to_user
            email: foo@foo.io

            YAML,
        ];

        yield [
            new AssignToUser(null, null, 'user_login'),
            <<<YAML
            action: assign_segment_to_user
            login: user_login

            YAML,
        ];

        yield [
            new AssignToUser(42, 'foo@foo.io', 'user_login'),
            <<<YAML
            action: assign_segment_to_user
            id: 42
            email: foo@foo.io
            login: user_login

            YAML,
        ];
    }
}
