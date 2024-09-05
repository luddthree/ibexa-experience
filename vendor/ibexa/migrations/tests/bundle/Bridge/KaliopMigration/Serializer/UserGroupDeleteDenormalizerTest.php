<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Bridge\KaliopMigration\Serializer;

use Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\UserGroupDeleteDenormalizer;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Tests\Bundle\Migration\IbexaKernelTestCase;

final class UserGroupDeleteDenormalizerTest extends IbexaKernelTestCase
{
    private const USER_GROUP_ID = 1;

    /** @var \Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\UserGroupDeleteDenormalizer */
    private $denormalizer;

    protected function setUp(): void
    {
        $this->denormalizer = new UserGroupDeleteDenormalizer();
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
            'type' => 'user_group',
            'mode' => 'delete',
            'match' => [
                'id' => self::USER_GROUP_ID,
            ],
        ];

        $expectedResult = [
            'type' => 'user_group',
            'mode' => 'delete',
            'match' => [
                'field' => 'id',
                'value' => self::USER_GROUP_ID,
            ],
        ];

        yield [$data, $expectedResult];
    }

    public function testSupportsDenormalization(): void
    {
        $supports = $this->denormalizer->supportsDenormalization(null, StepInterface::class);
        self::assertFalse($supports);

        $supports = $this->denormalizer->supportsDenormalization([
            'type' => 'user_group',
            'mode' => 'delete',
        ], UserGroupDeleteDenormalizer::class);
        self::assertFalse($supports);

        $supports = $this->denormalizer->supportsDenormalization([
            'type' => 'user_group',
            'mode' => 'delete',
        ], StepInterface::class);
        self::assertTrue($supports);

        $supports = $this->denormalizer->supportsDenormalization(null, '');
        self::assertFalse($supports);
    }
}

class_alias(UserGroupDeleteDenormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Bridge\KaliopMigration\Serializer\UserGroupDeleteDenormalizerTest');
