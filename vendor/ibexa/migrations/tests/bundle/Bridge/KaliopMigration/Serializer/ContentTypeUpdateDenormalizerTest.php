<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Bridge\KaliopMigration\Serializer;

use Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\ContentTypeUpdateDenormalizer;
use Ibexa\Migration\ValueObject\Step\ContentTypeUpdateStep;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * @covers \Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\ContentTypeUpdateDenormalizer
 */
final class ContentTypeUpdateDenormalizerTest extends TestCase
{
    /** @var \Ibexa\Bundle\Migration\Bridge\KaliopMigration\Serializer\ContentTypeUpdateDenormalizer */
    private $denormalizer;

    protected function setUp(): void
    {
        $this->denormalizer = new ContentTypeUpdateDenormalizer();
        $this->denormalizer->setDenormalizer($this->createMock(DenormalizerInterface::class));
    }

    public function testDenormalizationWithoutLangProperty(): void
    {
        $result = $this->denormalizer->denormalize([
            'type' => 'content_type',
            'mode' => 'update',
            'match' => [
                'identifier' => '__IDENTIFIER__',
            ],
            'remove_drafts' => true,
            'attributes' => [
                [
                    'identifier' => 'age_restriction',
                    'type' => 'ezboolean',
                    'name' => [
                        '__LANG__' => 'Show Age Restriction',
                    ],
                    'position' => 83,
                ],
            ],
        ], ContentTypeUpdateStep::class);

        $commonExpectedResult = $this->prepareCommonExpectedResult();
        $commonExpectedResult['metadata']['mainTranslation'] = null;

        self::assertEquals($commonExpectedResult, $result);
    }

    public function testDenormalizationWithLangProperty(): void
    {
        $result = $this->denormalizer->denormalize([
            'type' => 'content_type',
            'mode' => 'update',
            'lang' => '__LANG__',
            'match' => [
                'identifier' => '__IDENTIFIER__',
            ],
            'remove_drafts' => true,
            'attributes' => [
                [
                    'identifier' => 'age_restriction',
                    'type' => 'ezboolean',
                    'name' => 'Show Age Restriction',
                    'position' => 83,
                ],
            ],
        ], ContentTypeUpdateStep::class);

        self::assertEquals([
            'mode' => 'update',
            'type' => 'content_type',
            'match' => [
                'field' => 'content_type_identifier',
                'value' => '__IDENTIFIER__',
            ],
            'metadata' => [
                'contentTypeGroups' => null,
                'mainTranslation' => '__LANG__',
                'nameSchema' => null,
                'urlAliasSchema' => null,
                'container' => null,
                'remoteId' => null,
                'translations' => [],
            ],
            'fields' => [
                [
                    'identifier' => 'age_restriction',
                    'type' => 'ezboolean',
                    'required' => null,
                    'translations' => [
                        '__LANG__' => [
                            'name' => 'Show Age Restriction',
                            'description' => '',
                        ],
                    ],
                    'position' => 83,
                    'searchable' => null,
                    'infoCollector' => null,
                    'translatable' => null,
                    'category' => null,
                    'defaultValue' => null,
                    'fieldSettings' => null,
                    'validatorConfiguration' => null,
                ],
            ],
            'actions' => [
                [
                    'action' => 'remove_drafts',
                    'value' => null,
                ],
            ],
        ], $result);
    }

    public function testFailingWhenLangIsNotDefinedInPropertyNorAttribute(): void
    {
        self::expectException(\InvalidArgumentException::class);
        self::expectExceptionMessage('In order to convert `content_type` `update`, it needs to have `lang` defined.');

        $this->denormalizer->denormalize([
            'type' => 'content_type',
            'mode' => 'update',
            'match' => [
                'identifier' => '__IDENTIFIER__',
            ],
            'remove_drafts' => true,
            'attributes' => [
                [
                    'identifier' => 'age_restriction',
                    'type' => 'ezboolean',
                    'name' => 'Show Age Restriction',
                    'position' => 83,
                ],
            ],
        ], ContentTypeUpdateStep::class);
    }

    /**
     * @return array<mixed>
     */
    private function prepareCommonExpectedResult(): array
    {
        return [
            'mode' => 'update',
            'type' => 'content_type',
            'match' => [
                'field' => 'content_type_identifier',
                'value' => '__IDENTIFIER__',
            ],
            'metadata' => [
                'contentTypeGroups' => null,
                'mainTranslation' => '__LANG__',
                'nameSchema' => null,
                'urlAliasSchema' => null,
                'container' => null,
                'remoteId' => null,
                'translations' => [],
            ],
            'fields' => [
                [
                    'identifier' => 'age_restriction',
                    'type' => 'ezboolean',
                    'required' => null,
                    'translations' => [
                        '__LANG__' => [
                            'name' => 'Show Age Restriction',
                            'description' => '',
                        ],
                    ],
                    'position' => 83,
                    'searchable' => null,
                    'infoCollector' => null,
                    'translatable' => null,
                    'category' => null,
                    'defaultValue' => null,
                    'fieldSettings' => null,
                    'validatorConfiguration' => null,
                ],
            ],
            'actions' => [
                [
                    'action' => 'remove_drafts',
                    'value' => null,
                ],
            ],
        ];
    }
}

class_alias(ContentTypeUpdateDenormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Bridge\KaliopMigration\Serializer\ContentTypeUpdateDenormalizerTest');
