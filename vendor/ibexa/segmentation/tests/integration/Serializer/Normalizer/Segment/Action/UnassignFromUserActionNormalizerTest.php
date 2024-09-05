<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Segmentation\Serializer\Normalizer\Segment\Action;

use Ibexa\Platform\Segmentation\Tests\integration\AbstractSerializerTestCase;
use Ibexa\Segmentation\Value\Step\Action\Segment\UnassignFromUser;

/**
 * @covers \Ibexa\Bundle\Segmentation\Serializer\Normalizer\Segment\Action\UnassignFromUserActionNormalizer
 */
final class UnassignFromUserActionNormalizerTest extends AbstractSerializerTestCase
{
    /**
     * @dataProvider provideDataForSerialization
     */
    public function testSerialization(UnassignFromUser $action, string $expected): void
    {
        $yaml = $this->serializer->serialize($action, 'yaml');

        self::assertSame($expected, $yaml);
    }

    /**
     * @return iterable<array{\Ibexa\Segmentation\Value\Step\Action\Segment\UnassignFromUser, non-empty-string}>
     */
    public function provideDataForSerialization(): iterable
    {
        yield [
            new UnassignFromUser(42),
            <<<YAML
            action: unassign_segment_from_user
            id: 42

            YAML,
        ];

        yield [
            new UnassignFromUser(null, 'foo@foo.io'),
            <<<YAML
            action: unassign_segment_from_user
            email: foo@foo.io

            YAML,
        ];

        yield [
            new UnassignFromUser(null, null, 'user_login'),
            <<<YAML
            action: unassign_segment_from_user
            login: user_login

            YAML,
        ];

        yield [
            new UnassignFromUser(42, 'foo@foo.io', 'user_login'),
            <<<YAML
            action: unassign_segment_from_user
            id: 42
            email: foo@foo.io
            login: user_login

            YAML,
        ];
    }
}
