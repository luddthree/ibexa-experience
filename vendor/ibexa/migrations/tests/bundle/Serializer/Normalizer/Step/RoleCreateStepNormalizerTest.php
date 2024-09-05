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
use Ibexa\Migration\ValueObject\Step\Role\CreateMetadata;
use Ibexa\Migration\ValueObject\Step\Role\Limitation;
use Ibexa\Migration\ValueObject\Step\Role\Policy;
use Ibexa\Migration\ValueObject\Step\RoleCreateStep;
use Ibexa\Tests\Bundle\Migration\Serializer\AbstractSerializationTestCase;
use Traversable;

/**
 * @covers \Ibexa\Bundle\Migration\Serializer\Normalizer\Step\RoleCreateStepNormalizer
 */
final class RoleCreateStepNormalizerTest extends AbstractSerializationTestCase
{
    public function provideForSerialization(): iterable
    {
        $expected = self::loadFile(__DIR__ . '/role--create/normalize/role--create.yaml');

        $metadata = new CreateMetadata('bar');
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
            new RoleCreateStep(
                $metadata,
                $policies,
                [
                    new ReferenceDefinition(
                        'ref__role__1__role_id',
                        'role_id',
                    ),
                ],
                [
                    new AssignToUserGroup(null, '42'),
                    new AssignToUserGroup(42),
                    new AssignToUser(42),
                    new AssignToUser(null, '42'),
                    new AssignToUser(null, null, 'foo'),
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
        $source = self::loadFile(__DIR__ . '/role--create/denormalize/role--create.yaml');

        $expectation = static function ($deserialized): void {
            self::assertIsIterable($deserialized);
            if ($deserialized instanceof Traversable) {
                $deserialized = iterator_to_array($deserialized);
            }
            /** @var \Ibexa\Migration\ValueObject\Step\RoleCreateStep[] $deserialized */
            self::assertContainsOnlyInstancesOf(RoleCreateStep::class, $deserialized);
            self::assertCount(1, $deserialized);

            $step = $deserialized[0];
            self::assertSame('foo', $step->metadata->identifier);
            self::assertCount(21, $step->policies);
            self::assertContainsOnlyInstancesOf(Policy::class, $step->policies);

            $limitations = array_reduce(
                $step->policies,
                static function (array $carry, Policy $policy): array {
                    if ($policy->limitations === null) {
                        return $carry;
                    }

                    return array_merge($carry, $policy->limitations);
                },
                []
            );
            self::assertContainsOnlyInstancesOf(Limitation::class, $limitations);
            self::assertCount(22, $limitations);
            self::assertCount(5, $step->getActions());
        };

        yield [
            $source,
            $expectation,
        ];
    }
}

class_alias(RoleCreateStepNormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Serializer\Normalizer\Step\RoleCreateStepNormalizerTest');
