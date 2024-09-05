<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Bridge\KaliopMigration\Serializer;

use Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\ContentTypeCreateDenormalizer;
use Ibexa\Migration\ValueObject\Step\ContentTypeCreateStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Tests\Bundle\Migration\IbexaKernelTestCase;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class ContentTypeCreateDenormalizerTest extends IbexaKernelTestCase
{
    /**
     * @var \Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\ContentTypeCreateDenormalizer
     */
    private $denormalizer;

    protected function setUp(): void
    {
        $this->denormalizer = new ContentTypeCreateDenormalizer();
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
            'type' => 'content_type',
            'mode' => 'create',
            'content_type_group' => '__CONTENT_GROUP__',
            'identifier' => '__IDENTIFIER__',
            'name' => '__NAME__',
            'description' => '__DESCRIPTION__',
            'name_pattern' => '__NAME_PATTERN__',
            'url_name_pattern' => '__URL_ALIAS_SCHEMA__',
            'is_container' => false,
            'section' => '__SECTION__',
            'lang' => '__LANG__',
            'attributes' => [
                [
                    'identifier' => '__IDENTIFIER__',
                    'type' => '__TYPE__',
                    'name' => 'ATTRIBUTE_NAME_',
                    'description' => 'ATTRIBUTE_DESC__',
                    'required' => true,
                    'searchable' => false,
                    'disable-translation' => false,
                    'default-value' => '__DEFAULT_VALUE__',
                ],
            ],
        ];

        $expectedResult = [
            'type' => 'content_type',
            'mode' => 'create',
            'metadata' => [
                'identifier' => '__IDENTIFIER__',
                'contentTypeGroups' => ['__CONTENT_GROUP__'],
                'mainTranslation' => '__LANG__',
                'nameSchema' => '__NAME_PATTERN__',
                'urlAliasSchema' => '__URL_ALIAS_SCHEMA__',
                'container' => false,
                'remoteId' => null,
                'translations' => [
                    '__LANG__' => [
                        'name' => '__NAME__',
                        'description' => '__DESCRIPTION__',
                    ],
                ],
            ],
            'fields' => [
                [
                    'identifier' => '__IDENTIFIER__',
                    'type' => '__TYPE__',
                    'position' => null,
                    'translations' => [
                        '__LANG__' => [
                            'name' => 'ATTRIBUTE_NAME_',
                            'description' => 'ATTRIBUTE_DESC__',
                        ],
                    ],
                    'required' => true,
                    'searchable' => false,
                    'infoCollector' => null,
                    'translatable' => true,
                    'category' => null,
                    'defaultValue' => '__DEFAULT_VALUE__',
                    'fieldSettings' => null,
                    'validatorConfiguration' => null,
                ],
            ],
        ];

        yield [$data, $expectedResult];

        $data['description'] = [];
        $expectedResult['metadata']['translations']['__LANG__']['description'] = '';

        yield [$data, $expectedResult];
    }

    public function testSupportsDenormalization(): void
    {
        $supports = $this->denormalizer->supportsDenormalization(null, StepInterface::class);
        self::assertFalse($supports);

        $supports = $this->denormalizer->supportsDenormalization([
            'type' => 'content_type',
            'mode' => 'create',
        ], ContentTypeCreateStep::class);
        self::assertFalse($supports);

        $supports = $this->denormalizer->supportsDenormalization([
            'type' => 'content_type',
            'mode' => 'create',
        ], StepInterface::class);
        self::assertTrue($supports);

        $supports = $this->denormalizer->supportsDenormalization(null, '');
        self::assertFalse($supports);
    }
}

class_alias(ContentTypeCreateDenormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Bridge\KaliopMigration\Serializer\ContentTypeCreateDenormalizerTest');
