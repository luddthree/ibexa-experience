<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Serializer\Normalizer\Step;

use DateTime;
use Ibexa\Bundle\Migration\Serializer\Normalizer\Step\ContentTypeGroup\ContentTypeGroupUpdateStepNormalizer;
use Ibexa\Core\Repository\Values\ContentType\ContentTypeGroup;
use Ibexa\Migration\ValueObject\ContentTypeGroup\Matcher;
use Ibexa\Migration\ValueObject\ContentTypeGroup\UpdateMetadata;
use Ibexa\Migration\ValueObject\Step\ContentTypeGroupUpdateStep;
use Ibexa\Tests\Bundle\Migration\Serializer\AbstractSerializationTestCase;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Traversable;

final class ContentTypeGroupUpdateStepNormalizerTest extends AbstractSerializationTestCase
{
    /** @var \Ibexa\Bundle\Migration\Serializer\Normalizer\Step\ContentTypeGroup\ContentTypeGroupUpdateStepNormalizer */
    private $normalizer;

    /** @var \PHPUnit\Framework\MockObject\MockObject|\Symfony\Component\Serializer\Normalizer\DenormalizerInterface */
    private $subDenormalizer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->normalizer = $this->buildNormalizer();
    }

    private function buildNormalizer(): ContentTypeGroupUpdateStepNormalizer
    {
        $normalizer = new ContentTypeGroupUpdateStepNormalizer();
        $subNormalizer = $this->createMock(NormalizerInterface::class);
        $normalizer->setNormalizer($subNormalizer);
        $this->subDenormalizer = $this->createMock(DenormalizerInterface::class);
        $normalizer->setDenormalizer($this->subDenormalizer);

        return $normalizer;
    }

    public function testNormalize(): void
    {
        $step = new ContentTypeGroupUpdateStep(
            UpdateMetadata::createFromArray([
                'identifier' => 'Baz',
                'modifierId' => 14,
                'modificationDate' => '2020-11-13 00:00:00',
            ]),
            new Matcher(Matcher::CONTENT_TYPE_NAME_IDENTIFIER, 'identifier'),
        );
        $normalized = $this->normalizer->normalize($step);

        self::assertArrayHasKey('type', $normalized);
        self::assertArrayHasKey('mode', $normalized);
        self::assertSame([
            'type' => 'content_type_group',
            'mode' => 'update',
            'match' => null,
            'metadata' => null,
        ], $normalized);
    }

    public function testDenormalize(): void
    {
        $metadata = UpdateMetadata::createFromArray([
            'identifier' => 'Baz',
            'modificationDate' => '2020-11-13 00:00:00',
            'modifierId' => 14,
        ]);

        $match = new Matcher(Matcher::CONTENT_TYPE_NAME_IDENTIFIER, 'identifier');

        $this->subDenormalizer->method(
            'denormalize'
        )->withConsecutive(
            [[], UpdateMetadata::class],
            [[], Matcher::class],
        )->willReturnOnConsecutiveCalls(
            $metadata,
            $match,
        );

        $denormalized = $this->normalizer->denormalize(
            [
                'type' => 'content_type_group',
                'mode' => 'update',
                'match' => [],
                'metadata' => [],
            ],
            ContentTypeGroupUpdateStep::class
        );

        $step = new ContentTypeGroupUpdateStep(
            $metadata,
            $match
        );

        self::assertEquals($step, $denormalized);
    }

    public function provideForSerialization(): iterable
    {
        $expected = self::loadFile(__DIR__ . '/content-type-group--update/content-type-group--update.yaml');

        $contentTypeGroup = new ContentTypeGroup([
            'identifier' => 'Baz',
            'modificationDate' => new DateTime('2020-11-25T18:22:23+00:00'),
            'modifierId' => 14,
        ]);

        $step = new ContentTypeGroupUpdateStep(
            UpdateMetadata::create($contentTypeGroup),
            new Matcher(Matcher::CONTENT_TYPE_NAME_IDENTIFIER, 'identifier'),
        );

        yield [
            [$step],
            $expected,
        ];
    }

    public function provideForDeserialization(): iterable
    {
        $source = self::loadFile(__DIR__ . '/content-type-group--update/content-type-group--update.yaml');

        $expectation = static function ($deserialized): void {
            self::assertIsIterable($deserialized);
            if ($deserialized instanceof Traversable) {
                $deserialized = iterator_to_array($deserialized);
            }
            self::assertContainsOnlyInstancesOf(ContentTypeGroupUpdateStep::class, $deserialized);

            $metadata = UpdateMetadata::createFromArray([
                'identifier' => 'Baz',
                'modificationDate' => '2020-11-25T18:22:23+00:00',
                'modifierId' => 14,
            ]);

            $match = new Matcher(Matcher::CONTENT_TYPE_NAME_IDENTIFIER, 'identifier');

            $expectedStep = new ContentTypeGroupUpdateStep(
                $metadata,
                $match
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

class_alias(ContentTypeGroupUpdateStepNormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Serializer\Normalizer\Step\ContentTypeGroupUpdateStepNormalizerTest');
