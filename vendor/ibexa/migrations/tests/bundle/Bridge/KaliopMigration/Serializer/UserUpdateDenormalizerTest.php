<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Bridge\KaliopMigration\Serializer;

use Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\UserUpdateDenormalizer;
use Ibexa\Migration\ValueObject\Step\ContentTypeCreateStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Tests\Bundle\Migration\IbexaKernelTestCase;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class UserUpdateDenormalizerTest extends IbexaKernelTestCase
{
    private const USER_ID = 123;

    /** @var \Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\UserUpdateDenormalizer */
    private $denormalizer;

    protected function setUp(): void
    {
        $this->denormalizer = new UserUpdateDenormalizer();
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

    /**
     * @return iterable<array{
     *      array<mixed>,
     *      array<mixed>
     * }>
     */
    public function provideDenormalize(): iterable
    {
        $data = [
            'type' => 'user',
            'mode' => 'update',
            'match' => [
                'id' => self::USER_ID,
            ],
            'first_name' => '__FIRST_NAME__',
            'last_name' => '__LAST_NAME__',
            'username' => '__USERNAME__',
            'email' => 'editor@emailbox.com',
            'password' => '__PASSWORD__',
            'enabled' => true,
        ];

        $expectedResult = [
            'type' => 'user',
            'mode' => 'update',
            'match' => [
                'field' => 'id',
                'value' => self::USER_ID,
            ],
            'metadata' => [
                'login' => '__USERNAME__',
                'email' => 'editor@emailbox.com',
                'password' => '__PASSWORD__',
                'enabled' => true,
            ],
            'fields' => [
                [
                    'fieldDefIdentifier' => 'first_name',
                    'fieldTypeIdentifier' => 'ezstring',
                    'value' => '__FIRST_NAME__',
                ],
                [
                    'fieldDefIdentifier' => 'last_name',
                    'fieldTypeIdentifier' => 'ezstring',
                    'value' => '__LAST_NAME__',
                ],
            ],
        ];

        yield 'user with user id' => [$data, $expectedResult];
    }

    public function testSupportsDenormalization(): void
    {
        $supports = $this->denormalizer->supportsDenormalization(null, StepInterface::class);
        self::assertFalse($supports);

        $supports = $this->denormalizer->supportsDenormalization([
            'type' => 'user',
            'mode' => 'update',
        ], ContentTypeCreateStep::class);
        self::assertFalse($supports);

        $supports = $this->denormalizer->supportsDenormalization([
            'type' => 'user',
            'mode' => 'update',
        ], StepInterface::class);
        self::assertTrue($supports);

        $supports = $this->denormalizer->supportsDenormalization(null, '');
        self::assertFalse($supports);
    }
}

class_alias(UserUpdateDenormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Bridge\KaliopMigration\Serializer\UserUpdateDenormalizerTest');
