<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Bridge\KaliopMigration\Serializer;

use Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\UserCreateDenormalizer;
use Ibexa\Migration\ValueObject\Step\ContentTypeCreateStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Tests\Bundle\Migration\IbexaKernelTestCase;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class UserCreateDenormalizerTest extends IbexaKernelTestCase
{
    private const USER_GROUP_IDENTIFIER = '__USER_GROUP_IDENTIFIER__';

    /** @var \Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\UserCreateDenormalizer */
    private $denormalizer;

    protected function setUp(): void
    {
        $this->denormalizer = new UserCreateDenormalizer();
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
     * @return iterable<string, array{
     *      array<mixed>,
     *      array<mixed>,
     * }>
     */
    public function provideDenormalize(): iterable
    {
        $data = [
            'type' => 'user',
            'mode' => 'create',
            'first_name' => '__FIRST_NAME__',
            'last_name' => '__LAST_NAME__',
            'username' => '__USERNAME__',
            'email' => 'editor@emailbox.com',
            'password' => '__PASSWORD__',
            'groups' => [self::USER_GROUP_IDENTIFIER],
        ];

        $expectedResult = [
            'type' => 'user',
            'mode' => 'create',
            'metadata' => [
                'login' => '__USERNAME__',
                'email' => 'editor@emailbox.com',
                'password' => '__PASSWORD__',
                'contentType' => 'user',
            ],
            'groups' => [self::USER_GROUP_IDENTIFIER],
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

        yield 'Baseline data' => [
            $data,
            $expectedResult,
        ];
    }

    public function testSupportsDenormalization(): void
    {
        $supports = $this->denormalizer->supportsDenormalization(null, StepInterface::class);
        self::assertFalse($supports);

        $supports = $this->denormalizer->supportsDenormalization([
            'type' => 'user',
            'mode' => 'create',
        ], ContentTypeCreateStep::class);
        self::assertFalse($supports);

        $supports = $this->denormalizer->supportsDenormalization([
            'type' => 'user',
            'mode' => 'create',
        ], StepInterface::class);
        self::assertTrue($supports);

        $supports = $this->denormalizer->supportsDenormalization(null, '');
        self::assertFalse($supports);
    }
}

class_alias(UserCreateDenormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Bridge\KaliopMigration\Serializer\UserCreateDenormalizerTest');
