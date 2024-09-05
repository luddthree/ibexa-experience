<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Serializer\Normalizer\Step;

use Ibexa\Core\FieldType\TextLine\Value;
use Ibexa\Core\Repository\Values\ContentType\FieldDefinition as ApiFieldDefinition;
use Ibexa\Migration\ValueObject\ContentType\FieldDefinition;
use Ibexa\Migration\ValueObject\ContentType\FieldDefinitionCollection;
use Ibexa\Migration\ValueObject\ContentType\Matcher;
use Ibexa\Migration\ValueObject\ContentType\UpdateMetadata;
use Ibexa\Migration\ValueObject\Step\Action\ContentType\RemoveDrafts;
use Ibexa\Migration\ValueObject\Step\ContentTypeUpdateStep;
use Ibexa\Tests\Bundle\Migration\Serializer\AbstractSerializationTestCase;
use Traversable;

/**
 * @covers \Ibexa\Bundle\Migration\Serializer\Normalizer\Step\ContentTypeUpdateStepNormalizer
 */
final class ContentTypeUpdateStepNormalizerTest extends AbstractSerializationTestCase
{
    public function provideForSerialization(): iterable
    {
        $source = self::loadFile(__DIR__ . '/content-type--update/content-type--update.yaml');

        $expected = strtr($source, [
            '{{ match_field }}' => 'content_type_identifier',
        ]);

        $metadata = UpdateMetadata::createFromArray([
            'identifier' => 'blog_foo',
            'mainTranslation' => 'eng-GB',
            'modifierId' => 14,
            'modificationDate' => '2020-09-17T13:22:00+00:00',
            'remoteId' => '323c1b32e506163c357552edd81706b4',
            'urlAliasSchema' => '',
            'nameSchema' => '<name>',
            'container' => true,
            'defaultAlwaysAvailable' => true,
            'defaultSortField' => 2,
            'defaultSortOrder' => 0,
            'contentTypeGroups' => ['Content'],
            'translations' => [
                'eng-GB' => [
                    'name' => 'Blog',
                    'description' => 'Defines a structure for storing blog posts (short articles by a single person and/or on a particular topic).',
                ],
            ],
        ]);

        $fieldDefinitionCollection = FieldDefinitionCollection::create([
            FieldDefinition::fromAPIFieldDefinition(
                new ApiFieldDefinition([
                    'identifier' => 'name',
                    'fieldTypeIdentifier' => 'ezstring',
                    'position' => 1,
                    'fieldGroup' => 'content',
                    'isRequired' => false,
                    'isSearchable' => true,
                    'isTranslatable' => true,
                    'isThumbnail' => null,
                    'isInfoCollector' => false,
                    'validatorConfiguration' => [
                        'StringLengthValidator' => [
                            'maxStringLength' => 255,
                            'minStringLength' => null,
                        ],
                    ],
                    'names' => [
                        'eng-GB' => 'Name',
                    ],
                    'descriptions' => [
                        'eng-GB' => 'Name or title of the blog.',
                    ],
                ]),
                new Value('New article')
            ),
        ]);

        $matcher = new Matcher('content_type_identifier', '__CONTENT_TYPE_IDENTIFIER__');
        $actions = [
            new RemoveDrafts(),
        ];

        $data = [new ContentTypeUpdateStep($metadata, $fieldDefinitionCollection, $matcher, $actions)];

        yield [
            $data,
            $expected,
        ];

        $expected = strtr($source, [
            '{{ match_field }}' => 'location_remote_id',
        ]);

        $matcher = new Matcher('location_remote_id', '__CONTENT_TYPE_IDENTIFIER__');
        $data = [new ContentTypeUpdateStep($metadata, $fieldDefinitionCollection, $matcher, $actions)];

        yield [
            $data,
            $expected,
        ];
    }

    public function provideForDeserialization(): iterable
    {
        $source = self::loadFile(__DIR__ . '/content-type--update/content-type--update.yaml');

        yield [
            strtr($source, [
                '{{ match_field }}' => 'content_type_identifier',
            ]),
            $this->generateDeserializationExpectationClosure('content_type_identifier'),
        ];

        yield [
            strtr($source, [
                '{{ match_field }}' => 'location_remote_id',
            ]),
            $this->generateDeserializationExpectationClosure('location_remote_id'),
        ];
    }

    private function generateDeserializationExpectationClosure(string $matchField): callable
    {
        return static function ($deserialized) use ($matchField): void {
            self::assertIsIterable($deserialized);
            if ($deserialized instanceof Traversable) {
                $deserialized = iterator_to_array($deserialized);
            }
            self::assertContainsOnlyInstancesOf(ContentTypeUpdateStep::class, $deserialized);

            $metadata = UpdateMetadata::createFromArray([
                'identifier' => 'blog_foo',
                'mainTranslation' => 'eng-GB',
                'modifierId' => 14,
                'modificationDate' => '2020-09-17T13:22:00+00:00',
                'remoteId' => '323c1b32e506163c357552edd81706b4',
                'urlAliasSchema' => '',
                'nameSchema' => '<name>',
                'container' => true,
                'defaultAlwaysAvailable' => true,
                'defaultSortField' => 2,
                'defaultSortOrder' => 0,
                'contentTypeGroups' => ['Content'],
                'translations' => [
                    'eng-GB' => [
                        'name' => 'Blog',
                        'description' => 'Defines a structure for storing blog posts (short articles by a single person and/or on a particular topic).',
                    ],
                ],
            ]);

            $fieldDefinitionCollection = FieldDefinitionCollection::create([
                FieldDefinition::fromAPIFieldDefinition(
                    new ApiFieldDefinition([
                        'identifier' => 'name',
                        'fieldTypeIdentifier' => 'ezstring',
                        'position' => 1,
                        'fieldGroup' => 'content',
                        'isRequired' => false,
                        'isSearchable' => true,
                        'isTranslatable' => true,
                        'isThumbnail' => null,
                        'isInfoCollector' => false,
                        'validatorConfiguration' => [
                            'StringLengthValidator' => [
                                'maxStringLength' => 255,
                                'minStringLength' => null,
                            ],
                        ],
                        'names' => [
                            'eng-GB' => 'Name',
                        ],
                        'descriptions' => [
                            'eng-GB' => 'Name or title of the blog.',
                        ],
                    ]),
                    new Value('New article')
                ),
            ]);

            $matcher = new Matcher($matchField, '__CONTENT_TYPE_IDENTIFIER__');
            $actions = [
                new RemoveDrafts(),
            ];

            $expectedStep = new ContentTypeUpdateStep(
                $metadata,
                $fieldDefinitionCollection,
                $matcher,
                $actions
            );

            self::assertCount(1, $deserialized);
            [$deserializedObject] = $deserialized;

            self::assertEquals($expectedStep, $deserializedObject);
        };
    }
}

class_alias(ContentTypeUpdateStepNormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Serializer\Normalizer\Step\ContentTypeUpdateStepNormalizerTest');
