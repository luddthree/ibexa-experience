<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Bridge\KaliopMigration\Serializer;

use Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\LocationUpdateDenormalizer;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Migration\ValueObject\Step\LocationUpdateStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Tests\Bundle\Migration\IbexaKernelTestCase;

final class LocationUpdateDenormalizerTest extends IbexaKernelTestCase
{
    /**
     * @var \Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\LocationUpdateDenormalizer
     */
    private $denormalizer;

    protected function setUp(): void
    {
        $this->denormalizer = new LocationUpdateDenormalizer();
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

    /**
     * @return iterable<array{
     *      array<mixed>,
     *      array<mixed>
     * }>
     */
    public function provideDenormalize(): iterable
    {
        $data = [
            'type' => 'location',
            'mode' => 'update',
            'sort_order' => 'ASC',
            'sort_field' => 'path',
            'remote_id' => '__REMOTE_ID__',
            'priority' => 100,
            'match' => [
                'location_id' => '__LOCATION_ID__',
            ],
        ];

        $expectedResult = [
            'type' => 'location',
            'mode' => 'update',
            'match' => [
                'field' => 'location_id',
                'value' => '__LOCATION_ID__',
            ],
            'metadata' => [
                'remoteId' => '__REMOTE_ID__',
                'priority' => 100,
                'sortField' => Location::SORT_FIELD_PATH,
                'sortOrder' => Location::SORT_ORDER_ASC,
            ],
        ];

        yield [$data, $expectedResult];
    }

    public function testSupportsDenormalization(): void
    {
        $supports = $this->denormalizer->supportsDenormalization(null, StepInterface::class);
        self::assertFalse($supports);

        $supports = $this->denormalizer->supportsDenormalization([
            'type' => 'location',
            'mode' => 'update',
        ], LocationUpdateStep::class);
        self::assertFalse($supports);

        $supports = $this->denormalizer->supportsDenormalization([
            'type' => 'location',
            'mode' => 'update',
        ], StepInterface::class);
        self::assertTrue($supports);

        $supports = $this->denormalizer->supportsDenormalization(null, '');
        self::assertFalse($supports);
    }
}

class_alias(LocationUpdateDenormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Bridge\KaliopMigration\Serializer\LocationUpdateDenormalizerTest');
