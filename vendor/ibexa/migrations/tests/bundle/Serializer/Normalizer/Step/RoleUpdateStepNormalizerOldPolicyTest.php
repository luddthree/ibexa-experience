<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Serializer\Normalizer\Step;

use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Ibexa\Migration\ValueObject\Step\Action\Role\AssignToUser;
use Ibexa\Migration\ValueObject\Step\Action\Role\AssignToUserGroup;
use Ibexa\Migration\ValueObject\Step\Role\Limitation;
use Ibexa\Migration\ValueObject\Step\Role\Matcher;
use Ibexa\Migration\ValueObject\Step\Role\Policy;
use Ibexa\Migration\ValueObject\Step\Role\PolicyList;
use Ibexa\Migration\ValueObject\Step\Role\UpdateMetadata;
use Ibexa\Migration\ValueObject\Step\RoleUpdateStep;
use Ibexa\Tests\Bundle\Migration\Serializer\AbstractSerializationTestCase;
use Traversable;

/**
 * @covers \Ibexa\Bundle\Migration\Serializer\Normalizer\Step\RoleUpdateStepNormalizer
 */
final class RoleUpdateStepNormalizerOldPolicyTest extends AbstractSerializationTestCase
{
    public function provideForSerialization(): iterable
    {
        $expected = self::loadFile(__DIR__ . '/role--update/role--update.yaml');

        $policies = [
            new Policy('user', 'login', [
                new Limitation('SiteAccess', [
                    '__default_site_access__',
                    '__second_site_access__',
                ]),
            ]),
            new Policy('user', 'password'),
            new Policy('content', 'read', [
                new Limitation('Section', [
                    'standard',
                    'users',
                ]),
            ]),
            new Policy('section', 'view'),
            new Policy('content', 'reverserelatedlist'),
            new Policy('content', 'create', [
                new Limitation('Section', [
                    'standard',
                    'users',
                ]),
                new Limitation('Class', [
                    'folder',
                    'article',
                    'user_group',
                    'user',
                    'image',
                    'file',
                    'landing_page',
                ]),
                new Limitation('Language', [
                    'eng-GB',
                ]),
            ]),
        ];
        $data = [
            new RoleUpdateStep(
                new Matcher('identifier', '__role_identifier__'),
                new UpdateMetadata('__role_identifier__'),
                new PolicyList($policies),
                [
                    new ReferenceDefinition(
                        'ref__role__1__role_id',
                        'role_id',
                    ),
                ],
            ),
            new RoleUpdateStep(
                new Matcher('id', '__id__'),
                new UpdateMetadata(null),
                new PolicyList($policies),
                [
                    new ReferenceDefinition(
                        'ref__role__1__role_id',
                        'role_id',
                    ),
                ],
                [
                    new AssignToUserGroup(42),
                    new AssignToUser(42),
                    new AssignToUserGroup(null, '42'),
                    new AssignToUser(null, '42'),
                    new AssignToUser(null, null, '42'),
                ],
            ),
        ];

        yield [
            $data,
            $expected,
        ];
    }

    public function provideForDeserialization(): iterable
    {
        $source = self::loadFile(__DIR__ . '/role--update/role--update-old-policy.yaml');

        $expectation = static function ($deserialized): void {
            self::assertIsIterable($deserialized);
            if ($deserialized instanceof Traversable) {
                $deserialized = iterator_to_array($deserialized);
            }
            self::assertContainsOnlyInstancesOf(RoleUpdateStep::class, $deserialized);
            self::assertCount(2, $deserialized);
        };

        yield [
            $source,
            $expectation,
        ];
    }
}
