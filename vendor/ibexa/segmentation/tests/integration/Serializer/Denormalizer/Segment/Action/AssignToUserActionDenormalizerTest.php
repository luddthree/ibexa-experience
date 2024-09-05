<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Segmentation\Serializer\Denormalizer\Segment\Action;

use Ibexa\Migration\ValueObject\Step\Action;
use Ibexa\Platform\Segmentation\Tests\integration\AbstractSerializerTestCase;
use Ibexa\Segmentation\Value\Step\Action\Segment\AssignToUser;

/**
 * @covers \Ibexa\Bundle\Segmentation\Serializer\Denormalizer\Segment\Action\AssignToUserActionDenormalizer
 */
final class AssignToUserActionDenormalizerTest extends AbstractSerializerTestCase
{
    public function testDeserialization(): void
    {
        $yaml = <<<YAML
            action: assign_segment_to_user
            id: 42
            YAML;

        $action = $this->serializer->deserialize($yaml, Action::class, 'yaml');

        self::assertInstanceOf(AssignToUser::class, $action);
        self::assertSame(42, $action->getId());
        self::assertNull($action->getEmail());
        self::assertNull($action->getLogin());

        $yaml = <<<YAML
            action: assign_segment_to_user
            email: foo@foo.io
            YAML;

        $action = $this->serializer->deserialize($yaml, Action::class, 'yaml');

        self::assertInstanceOf(AssignToUser::class, $action);
        self::assertNull($action->getId());
        self::assertSame('foo@foo.io', $action->getEmail());
        self::assertNull($action->getLogin());

        $yaml = <<<YAML
            action: assign_segment_to_user
            login: user_login
            YAML;

        $action = $this->serializer->deserialize($yaml, Action::class, 'yaml');

        self::assertInstanceOf(AssignToUser::class, $action);
        self::assertNull($action->getId());
        self::assertNull($action->getEmail());
        self::assertSame('user_login', $action->getLogin());
    }
}
