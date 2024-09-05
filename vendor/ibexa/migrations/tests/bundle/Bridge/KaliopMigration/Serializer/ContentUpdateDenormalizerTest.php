<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Bridge\KaliopMigration\Serializer;

use Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\ContentUpdateDenormalizer;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Migration\ValueObject\Step\ContentUpdateStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use PHPUnit\Framework\Constraint\IsType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * @covers \Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\ContentUpdateDenormalizer
 */
final class ContentUpdateDenormalizerTest extends TestCase
{
    /** @var \Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\ContentUpdateDenormalizer */
    private $denormalizer;

    protected function setUp(): void
    {
        $this->denormalizer = new ContentUpdateDenormalizer();
    }

    public function testDenormalize(): void
    {
        $data = [
            'type' => 'content',
            'mode' => 'update',
            'always_available' => true,
            'owner' => 14,
            'modification_date' => '__modification_date__',
            'publication_date' => '__publication_date__',
            'lang' => '__lang__',
            'match' => [
                'parent_location_id' => '__parent_location_id__',
            ],
            'attributes' => [
                'name' => '__NAME__',
            ],
        ];

        self::assertTrue($this->denormalizer->supportsDenormalization($data, StepInterface::class));

        $expectedResult = [
            'type' => 'content',
            'mode' => 'update',
            'metadata' => [
                'remoteId' => null,
                'alwaysAvailable' => true,
                'ownerId' => 14,
                'publishedData' => '__publication_date__',
                'modificationDate' => '__modification_date__',
                'mainLanguageCode' => null,
            ],
            'match' => '__MATCHER__',
            'fields' => [
                [
                    'fieldDefIdentifier' => 'name',
                    'languageCode' => '__lang__',
                    'value' => '__NAME__',
                ],
            ],
        ];

        $innerDenormalizer = $this->createMock(DenormalizerInterface::class);
        $innerDenormalizer
            ->expects(self::once())
            ->method('denormalize')
            ->with(
                new IsType(IsType::TYPE_ARRAY),
                self::identicalTo(Criterion::class),
            )
            ->willReturn('__MATCHER__');

        $this->denormalizer->setDenormalizer($innerDenormalizer);
        $result = $this->denormalizer->denormalize($data, StepInterface::class);

        self::assertEquals($expectedResult, $result);
    }

    public function testSupportsDenormalization(): void
    {
        $supports = $this->denormalizer->supportsDenormalization(null, StepInterface::class);
        self::assertFalse($supports);

        $supports = $this->denormalizer->supportsDenormalization([
            'type' => 'content',
            'mode' => 'update',
        ], ContentUpdateStep::class);
        self::assertFalse($supports);

        $supports = $this->denormalizer->supportsDenormalization([
            'type' => 'content',
            'mode' => 'update',
        ], StepInterface::class);
        self::assertTrue($supports);

        $supports = $this->denormalizer->supportsDenormalization(null, '');
        self::assertFalse($supports);
    }
}

class_alias(ContentUpdateDenormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Bridge\KaliopMigration\Serializer\ContentUpdateDenormalizerTest');
