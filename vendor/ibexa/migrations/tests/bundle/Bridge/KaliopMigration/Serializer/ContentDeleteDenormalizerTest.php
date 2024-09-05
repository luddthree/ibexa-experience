<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Bridge\KaliopMigration\Serializer;

use Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\ContentDeleteDenormalizer;
use Ibexa\Migration\ValueObject\Step\ContentDeleteStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * @covers \Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\ContentDeleteDenormalizer
 */
final class ContentDeleteDenormalizerTest extends TestCase
{
    public function testDenormalize(): void
    {
        $denormalizer = new ContentDeleteDenormalizer();
        $denormalizer->setDenormalizer($this->createConfiguredMock(DenormalizerInterface::class, [
            'denormalize' => '__MATCHER__',
        ]));

        $data = [
            'type' => 'content',
            'mode' => 'delete',
            'match' => [
                'parent_location_id' => '__parent_location_id__',
            ],
        ];

        $expectedResult = [
            'type' => 'content',
            'mode' => 'delete',
            'match' => '__MATCHER__',
        ];

        self::assertTrue($denormalizer->supportsDenormalization($data, StepInterface::class));
        $result = $denormalizer->denormalize($data, StepInterface::class);

        self::assertSame($expectedResult, $result);
    }

    public function testSupportsDenormalization(): void
    {
        $denormalizer = new ContentDeleteDenormalizer();
        $denormalizer->setDenormalizer($this->createConfiguredMock(DenormalizerInterface::class, [
            'denormalize' => '__MATCHER__',
        ]));

        $supports = $denormalizer->supportsDenormalization(null, StepInterface::class);
        self::assertFalse($supports);

        $supports = $denormalizer->supportsDenormalization([
            'type' => 'content',
            'mode' => 'delete',
        ], ContentDeleteStep::class);
        self::assertFalse($supports);

        $supports = $denormalizer->supportsDenormalization([
            'type' => 'content',
            'mode' => 'delete',
        ], StepInterface::class);
        self::assertTrue($supports);

        $supports = $denormalizer->supportsDenormalization(null, '');
        self::assertFalse($supports);
    }
}

class_alias(ContentDeleteDenormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Bridge\KaliopMigration\Serializer\ContentDeleteDenormalizerTest');
