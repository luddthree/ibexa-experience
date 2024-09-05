<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Bridge\KaliopMigration\Serializer;

use Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\ContentCreateDenormalizer;
use Ibexa\Migration\ValueObject\Step\ContentCreateStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Tests\Bundle\Migration\IbexaKernelTestCase;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * @covers \Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\ContentCreateDenormalizer
 */
final class ContentCreateDenormalizerTest extends IbexaKernelTestCase
{
    /** @var \Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\ContentCreateDenormalizer */
    private $denormalizer;

    protected function setUp(): void
    {
        $this->denormalizer = new ContentCreateDenormalizer();
        $this->denormalizer->setDenormalizer($this->createMock(DenormalizerInterface::class));
    }

    /**
     * @dataProvider provideDenormalize
     *
     * @param array<mixed> $data
     * @param array<mixed> $expectedResult
     */
    public function testDenormalize(array $data, array $expectedResult): void
    {
        self::assertTrue($this->denormalizer->supportsDenormalization($data, StepInterface::class));
        $result = $this->denormalizer->denormalize($data, StepInterface::class);

        self::assertSame($expectedResult, $result);
    }

    public function testSupportsDenormalization(): void
    {
        $supports = $this->denormalizer->supportsDenormalization(null, StepInterface::class);
        self::assertFalse($supports);

        $supports = $this->denormalizer->supportsDenormalization([
            'type' => 'content',
            'mode' => 'create',
        ], ContentCreateStep::class);
        self::assertFalse($supports);

        $supports = $this->denormalizer->supportsDenormalization([
            'type' => 'content',
            'mode' => 'create',
        ], StepInterface::class);
        self::assertTrue($supports);

        $supports = $this->denormalizer->supportsDenormalization(null, '');
        self::assertFalse($supports);
    }

    /**
     * @return iterable<array{
     *      array<mixed>,
     *      array<mixed>,
     * }>
     */
    public function provideDenormalize(): iterable
    {
        $baselineData = [
            'type' => 'content',
            'mode' => 'create',
            'content_type' => '__content_type_identifier__',
            'parent_location' => 1,
        ];

        $baselineResult = [
            'type' => 'content',
            'mode' => 'create',
            'metadata' => [
                'contentType' => '__content_type_identifier__',
                'mainTranslation' => null,
                'creatorId' => null,
                'modificationDate' => null,
                'publicationDate' => null,
                'remoteId' => null,
                'alwaysAvailable' => null,
                'section' => null,
            ],
            'location' => [
                'locationRemoteId' => null,
                'hidden' => null,
                'sortField' => null,
                'sortOrder' => null,
                'priority' => null,
                'parentLocationId' => 1,
            ],
            'fields' => [],
        ];

        yield 'Basic conversion' => [$baselineData, $baselineResult];

        $data = array_merge($baselineData, [
            'lang' => '__lang__',
            'attributes' => [
                'name' => '__foo_name__',
            ],
            'modification_date' => '__modification_date__',
            'publication_date' => '__publication_date__',
            'always_available' => true,
            'is_hidden' => true,
            'object_states' => ['lock'],
            'owner' => 14,
            'section' => '__foo_section__',
        ]);

        $expectedResult = array_merge($baselineResult, [
            'metadata' => [
                'contentType' => '__content_type_identifier__',
                'mainTranslation' => '__lang__',
                'creatorId' => 14,
                'modificationDate' => '__modification_date__',
                'publicationDate' => '__publication_date__',
                'remoteId' => null,
                'alwaysAvailable' => true,
                'section' => '__foo_section__',
            ],
            'location' => array_merge($baselineResult['location'], [
                'hidden' => true,
            ]),
            'fields' => [
                [
                    'fieldDefIdentifier' => 'name',
                    'languageCode' => '__lang__',
                    'value' => '__foo_name__',
                ],
            ],
        ]);

        yield 'Conversion with field & metadata' => [$data, $expectedResult];

        $data = array_merge($baselineData, [
            'lang' => '__lang__',
            'modification_date' => 1,
            'publication_date' => '1',
            'owner' => 14,
            'section' => '__foo_section__',
        ]);

        $expectedResult = array_merge($baselineResult, [
            'metadata' => [
                'contentType' => '__content_type_identifier__',
                'mainTranslation' => '__lang__',
                'creatorId' => 14,
                'modificationDate' => '1970-01-01T00:00:01+00:00',
                'publicationDate' => '1970-01-01T00:00:01+00:00',
                'remoteId' => null,
                'alwaysAvailable' => null,
                'section' => '__foo_section__',
            ],
        ]);

        yield 'Conversion with timestamps' => [$data, $expectedResult];
    }
}

class_alias(ContentCreateDenormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Bridge\KaliopMigration\Serializer\ContentCreateDenormalizerTest');
