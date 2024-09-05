<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Serializer\Normalizer\Step;

use DateTime;
use Ibexa\Bundle\Migration\Serializer\Normalizer\Step\ContentTypeCreateStepNormalizer;
use Ibexa\Core\FieldType;
use Ibexa\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Core\Repository\Values\ContentType\ContentTypeGroup;
use Ibexa\Core\Repository\Values\ContentType\FieldDefinition as ApiFieldDefinition;
use Ibexa\Core\Repository\Values\ContentType\FieldDefinitionCollection as ApiFieldDefinitionCollection;
use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Ibexa\Migration\Service\FieldTypeServiceInterface;
use Ibexa\Migration\ValueObject\ContentType\CreateMetadata;
use Ibexa\Migration\ValueObject\ContentType\FieldDefinition;
use Ibexa\Migration\ValueObject\ContentType\FieldDefinitionCollection;
use Ibexa\Migration\ValueObject\Step\ContentTypeCreateStep;
use Ibexa\Tests\Bundle\Migration\Serializer\AbstractSerializationTestCase;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Traversable;

final class ContentTypeCreateStepNormalizerTest extends AbstractSerializationTestCase
{
    /** @var \Ibexa\Bundle\Migration\Serializer\Normalizer\Step\ContentTypeCreateStepNormalizer */
    private $normalizer;

    /** @var \PHPUnit\Framework\MockObject\MockObject|\Symfony\Component\Serializer\Normalizer\DenormalizerInterface */
    private $subDenormalizer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buildNormalizer();
    }

    private function buildNormalizer(): void
    {
        $fieldTypeService = $this->createMock(FieldTypeServiceInterface::class);
        $this->normalizer = new ContentTypeCreateStepNormalizer($fieldTypeService);
        $subNormalizer = $this->createMock(NormalizerInterface::class);
        $this->normalizer->setNormalizer($subNormalizer);
        $this->subDenormalizer = $this->createMock(DenormalizerInterface::class);
        $this->normalizer->setDenormalizer($this->subDenormalizer);
    }

    public function testNormalize(): void
    {
        $step = new ContentTypeCreateStep(
            CreateMetadata::createFromArray([
                'identifier' => '1',
                'translations' => [],
                'contentTypeGroups' => [],
                'mainTranslation' => null,
                'creatorId' => 14,
                'creationDate' => '2020-11-13 00:00:00',
                'remoteId' => 'foo',
                'urlAliasSchema' => '<foo>',
                'nameSchema' => '<foo>',
                'container' => 'foo',
                'defaultAlwaysAvailable' => true,
                'defaultSortField' => 'foo',
                'defaultSortOrder' => 1,
            ]),
            FieldDefinitionCollection::create([]),
        );
        $normalized = $this->normalizer->normalize($step);

        self::assertArrayHasKey('type', $normalized);
        self::assertArrayHasKey('mode', $normalized);
        self::assertSame([
            'type' => 'content_type',
            'mode' => 'create',
            'metadata' => null,
            'fields' => null,
            'references' => null,
        ], $normalized);
    }

    public function testDenormalize(): void
    {
        $metadata = CreateMetadata::createFromArray([
            'identifier' => '1',
            'translations' => [],
            'contentTypeGroups' => [],
            'mainTranslation' => null,
            'creatorId' => 14,
            'creationDate' => '2020-11-13 00:00:00',
            'remoteId' => 'foo',
            'urlAliasSchema' => '<foo>',
            'nameSchema' => '<foo>',
            'container' => 'foo',
            'defaultAlwaysAvailable' => true,
            'defaultSortField' => 'foo',
            'defaultSortOrder' => 1,
        ]);

        $this->subDenormalizer->method(
            'denormalize'
        )->withConsecutive(
            [[], CreateMetadata::class],
            [[], FieldDefinitionCollection::class]
        )
         ->willReturnOnConsecutiveCalls(
             $metadata,
             FieldDefinitionCollection::create([])
         );

        $denormalized = $this->normalizer->denormalize(
            [
                'type' => 'content_type',
                'mode' => 'create',
                'metadata' => [],
                'fields' => [],
                'references' => [],
            ],
            ContentTypeCreateStep::class
        );

        $step = new ContentTypeCreateStep(
            $metadata,
            FieldDefinitionCollection::create([]),
        );

        self::assertEquals($step, $denormalized);
    }

    public function provideForSerialization(): iterable
    {
        $expected = self::loadFile(__DIR__ . '/content-type--create/normalize/content-type--create.yaml');

        $body = $intro = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<section xmlns="http://docbook.org/ns/docbook" xmlns:xlink="http://www.w3.org/1999/xlink" version="5.0-variant ezpublish-1.0"/>
XML;

        $titleFieldDefinition = new ApiFieldDefinition([
            'identifier' => 'title',
            'fieldTypeIdentifier' => 'ezstring',
            'position' => 1,
            'defaultValue' => 'New article',
            'fieldGroup' => '',
            'isRequired' => true,
            'isSearchable' => true,
            'isTranslatable' => true,
            'isThumbnail' => true,
            'isInfoCollector' => false,
            'validatorConfiguration' => [
                'StringLengthValidator' => [
                    'maxStringLength' => 255,
                    'minStringLength' => null,
                ],
            ],
            'names' => [
                'eng-GB' => 'Title',
            ],
            'descriptions' => [
                'eng-GB' => '',
            ],
        ]);

        $shortTitleFieldDefinition = new ApiFieldDefinition([
            'identifier' => 'short_title',
            'fieldTypeIdentifier' => 'ezstring',
            'position' => 2,
            'defaultValue' => new FieldType\TextLine\Value(''),
            'fieldGroup' => '',
            'isRequired' => false,
            'isSearchable' => true,
            'isTranslatable' => true,
            'isThumbnail' => true,
            'isInfoCollector' => false,
            'validatorConfiguration' => [
                'StringLengthValidator' => [
                    'maxStringLength' => 255,
                    'minStringLength' => null,
                ],
            ],
            'names' => [
                'eng-GB' => 'Short title',
            ],
            'descriptions' => [
                'eng-GB' => '',
            ],
        ]);

        $authorFieldDefinition = new ApiFieldDefinition([
            'identifier' => 'author',
            'fieldTypeIdentifier' => 'ezauthor',
            'position' => 3,
            'defaultValue' => new FieldType\Author\Value([]),
            'fieldGroup' => '',
            'isRequired' => false,
            'isSearchable' => false,
            'isTranslatable' => true,
            'isThumbnail' => false,
            'isInfoCollector' => false,
            'names' => [
                'eng-GB' => 'Author',
            ],
            'descriptions' => [
                'eng-GB' => '',
            ],
            'fieldSettings' => [
                'defaultAuthor' => 1,
            ],
        ]);

        $introFieldDefinition = new ApiFieldDefinition([
            'identifier' => 'intro',
            'fieldTypeIdentifier' => 'ezrichtext',
            'position' => 4,
            'defaultValue' => new FieldType\TextBlock\Value($intro),
            'fieldGroup' => '',
            'isRequired' => true,
            'isSearchable' => true,
            'isTranslatable' => true,
            'isThumbnail' => true,
            'isInfoCollector' => false,
            'names' => [
                'eng-GB' => 'Intro',
            ],
            'descriptions' => [
                'eng-GB' => '',
            ],
        ]);

        $bodyFieldDefinition = new ApiFieldDefinition([
            'identifier' => 'body',
            'fieldTypeIdentifier' => 'ezrichtext',
            'position' => 5,
            'defaultValue' => new FieldType\TextBlock\Value($body),
            'fieldGroup' => '',
            'isRequired' => false,
            'isSearchable' => true,
            'isTranslatable' => true,
            'isThumbnail' => true,
            'isInfoCollector' => false,
            'names' => [
                'eng-GB' => 'Body',
            ],
            'descriptions' => [
                'eng-GB' => '',
            ],
        ]);

        $enableCommentsFieldDefinition = new ApiFieldDefinition([
            'identifier' => 'enable_comments',
            'fieldTypeIdentifier' => 'ezboolean',
            'position' => 6,
            'defaultValue' => new FieldType\Checkbox\Value(false),
            'fieldGroup' => '',
            'isRequired' => false,
            'isSearchable' => false,
            'isTranslatable' => false,
            'isThumbnail' => false,
            'isInfoCollector' => false,
            'names' => [
                'eng-GB' => 'Enable comments',
            ],
            'descriptions' => [
                'eng-GB' => '',
            ],
        ]);

        $articleFieldDefinitions = FieldDefinitionCollection::create([
            FieldDefinition::fromAPIFieldDefinition(
                $titleFieldDefinition,
                new FieldType\TextLine\Value('New article')
            ),
            FieldDefinition::fromAPIFieldDefinition(
                $shortTitleFieldDefinition,
                new FieldType\TextLine\Value(''),
            ),
            FieldDefinition::fromAPIFieldDefinition(
                $authorFieldDefinition,
                new FieldType\Author\Value([])
            ),
            FieldDefinition::fromAPIFieldDefinition(
                $introFieldDefinition,
                new FieldType\TextBlock\Value($intro)
            ),
            FieldDefinition::fromAPIFieldDefinition(
                $bodyFieldDefinition,
                new FieldType\TextBlock\Value($body)
            ),
            FieldDefinition::fromAPIFieldDefinition(
                $enableCommentsFieldDefinition,
                new FieldType\Checkbox\Value(false),
            ),
        ]);

        $articleContentType = new ContentType([
            'identifier' => 'article',
            'mainLanguageCode' => 'eng-GB',
            'creatorId' => 14,
            'nameSchema' => '<short_title|title>',
            'urlAliasSchema' => '',
            'isContainer' => true,
            'remoteId' => 'c15b600eb9198b1924063b5a68758232',
            'creationDate' => new DateTime('2002-06-18T11:21:38+02:00'),
            'defaultAlwaysAvailable' => false,
            'defaultSortField' => 1,
            'defaultSortOrder' => 1,
            'contentTypeGroups' => [
                new ContentTypeGroup([
                    'identifier' => 'Content',
                    'id' => 1,
                ]),
            ],
            'names' => [
                'eng-GB' => 'Article',
            ],
            'descriptions' => [
                'eng-GB' => null,
            ],

            'fieldDefinitions' => new ApiFieldDefinitionCollection([
                $titleFieldDefinition,
                $shortTitleFieldDefinition,
                $authorFieldDefinition,
                $introFieldDefinition,
                $bodyFieldDefinition,
                $enableCommentsFieldDefinition,
            ]),
        ]);

        $article = new ContentTypeCreateStep(
            CreateMetadata::create($articleContentType),
            $articleFieldDefinitions,
            [
                new ReferenceDefinition('ref__content_type_id__article', 'content_type_id'),
            ]
        );

        $nameFieldDefinition = new ApiFieldDefinition([
            'identifier' => 'name',
            'fieldTypeIdentifier' => 'ezstring',
            'position' => 1,
            'defaultValue' => new FieldType\TextLine\Value(''),
            'fieldGroup' => 'content',
            'isRequired' => false,
            'isSearchable' => true,
            'isTranslatable' => true,
            'isThumbnail' => true,
            'isInfoCollector' => false,
            'validatorConfiguration' => [
                'StringLengthValidator' => [
                    'maxStringLength' => null,
                    'minStringLength' => null,
                ],
            ],
            'names' => [
                'eng-GB' => 'Name',
            ],
            'descriptions' => [
                'eng-GB' => 'Name or title of the blog.',
            ],
        ]);

        $blogFieldDefinitions = FieldDefinitionCollection::create([
            FieldDefinition::fromAPIFieldDefinition(
                $nameFieldDefinition,
                new FieldType\TextLine\Value(''),
            ),
        ]);

        $blogContentType = new ContentType([
            'identifier' => 'blog',
            'mainLanguageCode' => 'eng-GB',
            'creatorId' => 14,
            'nameSchema' => '<name>',
            'urlAliasSchema' => '',
            'isContainer' => true,
            'remoteId' => '323c1b32e506163c357552edd81706b4',
            'creationDate' => new DateTime('2020-09-17T15:22:00+02:00'),
            'defaultAlwaysAvailable' => true,
            'defaultSortField' => 2,
            'defaultSortOrder' => 0,
            'contentTypeGroups' => [
                new ContentTypeGroup([
                    'identifier' => 'Content',
                    'id' => 1,
                ]),
            ],
            'names' => [
                'eng-GB' => 'Blog',
            ],
            'descriptions' => [
                'eng-GB' => 'Defines a structure for storing blog posts (short articles by a single person and/or on a particular topic).',
            ],
            'fieldDefinitions' => new ApiFieldDefinitionCollection([$nameFieldDefinition]),
        ]);

        $blog = new ContentTypeCreateStep(
            CreateMetadata::create($blogContentType),
            $blogFieldDefinitions,
            [
                new ReferenceDefinition('ref__content_type_id__blog', 'content_type_id'),
            ]
        );

        yield [
            [$article, $blog],
            $expected,
        ];
    }

    public function provideForDeserialization(): iterable
    {
        $source = self::loadFile(__DIR__ . '/content-type--create/denormalize/content-type--create.yaml');

        $expectation = static function ($deserialized): void {
            self::assertIsIterable($deserialized);
            if ($deserialized instanceof Traversable) {
                $deserialized = iterator_to_array($deserialized);
            }
            self::assertContainsOnlyInstancesOf(ContentTypeCreateStep::class, $deserialized);

            $metadata = CreateMetadata::createFromArray([
                'identifier' => 'blog_foo',
                'mainTranslation' => 'eng-GB',
                'creatorId' => 14,
                'creationDate' => '2020-09-17T13:22:00+00:00',
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
                    'New article'
                ),
            ]);

            $references = [];

            $expectedStep = new ContentTypeCreateStep(
                $metadata,
                $fieldDefinitionCollection,
                $references
            );

            self::assertCount(1, $deserialized);
            [$deserializedObject] = $deserialized;

            self::assertEquals($expectedStep, $deserializedObject);
        };

        yield [
            $source,
            $expectation,
        ];
    }
}

class_alias(ContentTypeCreateStepNormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Serializer\Normalizer\Step\ContentTypeCreateStepNormalizerTest');
