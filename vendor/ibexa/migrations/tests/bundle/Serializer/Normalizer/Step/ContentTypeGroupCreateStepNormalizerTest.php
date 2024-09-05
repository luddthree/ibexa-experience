<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Serializer\Normalizer\Step;

use DateTime;
use Ibexa\Bundle\Migration\Serializer\Normalizer\Step\ContentTypeGroup\ContentTypeGroupCreateStepNormalizer;
use Ibexa\Core\Repository\Values\ContentType\ContentTypeGroup;
use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Ibexa\Migration\ValueObject\ContentTypeGroup\CreateMetadata;
use Ibexa\Migration\ValueObject\Step\ContentTypeGroupCreateStep;
use Ibexa\Tests\Bundle\Migration\Serializer\AbstractSerializationTestCase;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Traversable;

final class ContentTypeGroupCreateStepNormalizerTest extends AbstractSerializationTestCase
{
    /** @var \Ibexa\Bundle\Migration\Serializer\Normalizer\Step\ContentTypeGroup\ContentTypeGroupCreateStepNormalizer */
    private $normalizer;

    /** @var \PHPUnit\Framework\MockObject\MockObject|\Symfony\Component\Serializer\Normalizer\DenormalizerInterface */
    private $subDenormalizer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buildNormalizer();
    }

    private function buildNormalizer(): void
    {
        $this->normalizer = new ContentTypeGroupCreateStepNormalizer();
        $subNormalizer = $this->createMock(NormalizerInterface::class);
        $this->normalizer->setNormalizer($subNormalizer);
        $this->subDenormalizer = $this->createMock(DenormalizerInterface::class);
        $this->normalizer->setDenormalizer($this->subDenormalizer);
    }

    public function testNormalize(): void
    {
        $step = new ContentTypeGroupCreateStep(
            CreateMetadata::createFromArray([
                'identifier' => 'Baz',
                'creatorId' => 14,
                'creationDate' => '2020-11-13 00:00:00',
            ])
        );
        $normalized = $this->normalizer->normalize($step);

        self::assertArrayHasKey('type', $normalized);
        self::assertArrayHasKey('mode', $normalized);
        self::assertSame([
            'type' => 'content_type_group',
            'mode' => 'create',
            'metadata' => null,
            'references' => null,
        ], $normalized);
    }

    public function testDenormalize(): void
    {
        $metadata = CreateMetadata::createFromArray([
            'identifier' => 'Baz',
            'creatorId' => 14,
            'creationDate' => '2020-11-13 00:00:00',
        ]);

        $this->subDenormalizer->method(
            'denormalize'
        )->withConsecutive(
            [[], CreateMetadata::class]
        )->willReturnOnConsecutiveCalls(
            $metadata
        );

        $denormalized = $this->normalizer->denormalize(
            [
                'type' => 'content_type',
                'mode' => 'create',
                'metadata' => [],
                'fields' => [],
                'references' => [],
            ],
            ContentTypeGroupCreateStep::class
        );

        $step = new ContentTypeGroupCreateStep(
            $metadata
        );

        self::assertEquals($step, $denormalized);
    }

    public function provideForSerialization(): iterable
    {
        $expected = self::loadFile(__DIR__ . '/content-type-group--create/content-type-group--create.yaml');

        $bazContentTypeGroup = new ContentTypeGroup([
            'identifier' => 'Baz',
            'creationDate' => new DateTime('2020-11-25T18:22:23+00:00'),
            'creatorId' => 14,
        ]);

        $baz = new ContentTypeGroupCreateStep(
            CreateMetadata::create($bazContentTypeGroup),
            [
                new ReferenceDefinition('ref__Baz__content_type_group_id', 'content_type_group_id'),
            ]
        );

        yield [
            [$baz],
            $expected,
        ];
    }

    public function provideForDeserialization(): iterable
    {
        $source = self::loadFile(__DIR__ . '/content-type-group--create/content-type-group--create.yaml');

        $expectation = static function ($deserialized): void {
            self::assertIsIterable($deserialized);
            if ($deserialized instanceof Traversable) {
                $deserialized = iterator_to_array($deserialized);
            }
            self::assertContainsOnlyInstancesOf(ContentTypeGroupCreateStep::class, $deserialized);

            $metadata = CreateMetadata::createFromArray([
                'identifier' => 'Baz',
                'creationDate' => '2020-11-25T18:22:23+00:00',
                'creatorId' => 14,
            ]);

            $references = [];

            $expectedStep = new ContentTypeGroupCreateStep(
                $metadata,
                $references
            );

            self::assertCount(1, $deserialized);
            [$deserializedObject] = $deserialized;

            self::assertEquals($expectedStep, $deserializedObject);
        };

        yield [
            $source,
            $expectation,
        ];
    }
}

class_alias(ContentTypeGroupCreateStepNormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Serializer\Normalizer\Step\ContentTypeGroupCreateStepNormalizerTest');
