<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Bridge\KaliopMigration\Serializer;

use Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\UserGroupCreateDenormalizer;
use Ibexa\Contracts\Core\Repository\LanguageService;
use Ibexa\Migration\ValueObject\Step\ContentTypeCreateStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Tests\Bundle\Migration\IbexaKernelTestCase;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class UserGroupCreateDenormalizerTest extends IbexaKernelTestCase
{
    /** @var \Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\UserGroupCreateDenormalizer */
    private $denormalizer;

    protected function setUp(): void
    {
        $languageService = $this->createMock(LanguageService::class);
        $languageService
            ->method('getDefaultLanguageCode')
            ->willReturn('__LANG__');

        $this->denormalizer = new UserGroupCreateDenormalizer($languageService);
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
            'type' => 'user_group',
            'mode' => 'create',
            'parent_group_id' => '__PARENT_GROUP_ID__',
            'remote_id' => '__REMOTE_ID__',
            'name' => '__NAME__',
            'description' => '__DESCRIPTION__',
            'lang' => '__LANG__',
        ];

        $expectedResult = [
            'type' => 'user_group',
            'mode' => 'create',
            'metadata' => [
                'contentTypeIdentifier' => 'user_group',
                'mainLanguage' => '__LANG__',
                'parentGroupId' => '__PARENT_GROUP_ID__',
                'remoteId' => '__REMOTE_ID__',
            ],
            'fields' => [
                [
                    'fieldDefIdentifier' => 'name',
                    'fieldTypeIdentifier' => 'ezstring',
                    'languageCode' => '__LANG__',
                    'value' => '__NAME__',
                ], [
                    'fieldDefIdentifier' => 'description',
                    'fieldTypeIdentifier' => 'ezstring',
                    'languageCode' => '__LANG__',
                    'value' => '__DESCRIPTION__',
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
            'type' => 'user_group',
            'mode' => 'create',
        ], ContentTypeCreateStep::class);
        self::assertFalse($supports);

        $supports = $this->denormalizer->supportsDenormalization([
            'type' => 'user_group',
            'mode' => 'create',
        ], StepInterface::class);
        self::assertTrue($supports);

        $supports = $this->denormalizer->supportsDenormalization(null, '');
        self::assertFalse($supports);
    }
}

class_alias(UserGroupCreateDenormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Bridge\KaliopMigration\Serializer\UserGroupCreateDenormalizerTest');
