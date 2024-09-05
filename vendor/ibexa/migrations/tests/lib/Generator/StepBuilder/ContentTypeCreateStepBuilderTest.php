<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Migration\Generator\StepBuilder;

use DateTime;
use Ibexa\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Core\Repository\Values\ContentType\ContentTypeGroup;
use Ibexa\Core\Repository\Values\ContentType\FieldDefinition as ApiFieldDefinition;
use Ibexa\Core\Repository\Values\ContentType\FieldDefinitionCollection as ApiFieldDefinitionCollection;
use Ibexa\Migration\Generator\Reference\ContentTypeGenerator as ReferenceContentTypeGenerator;
use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Ibexa\Migration\Generator\StepBuilder\ContentTypeCreateStepBuilder;
use Ibexa\Migration\ValueObject\ContentType\CreateMetadata;
use Ibexa\Migration\ValueObject\ContentType\FieldDefinition;
use Ibexa\Migration\ValueObject\ContentType\FieldDefinitionCollection;
use Ibexa\Migration\ValueObject\Step\ContentTypeCreateStep;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class ContentTypeCreateStepBuilderTest extends TestCase
{
    /** @var \Ibexa\Migration\Generator\StepBuilder\ContentTypeCreateStepBuilder */
    private $builder;

    protected function setUp(): void
    {
        $this->builder = new ContentTypeCreateStepBuilder(
            new ReferenceContentTypeGenerator()
        );
    }

    public function testBuild(): void
    {
        $contentType = $this->createContentType();

        $result = $this->builder->build($contentType);

        $expectedContentTypeCreateStep = new ContentTypeCreateStep(
            CreateMetadata::create($contentType),
            $this->createFieldDefinitionCollection(),
            [
                new ReferenceDefinition('ref__blog__content_type_id', 'content_type_id'),
            ]
        );

        Assert::assertEquals($expectedContentTypeCreateStep, $result);
    }

    private function createContentType(): ContentType
    {
        return new ContentType([
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
            'fieldDefinitions' => new ApiFieldDefinitionCollection([
                new ApiFieldDefinition([
                    'identifier' => 'name',
                ]),
            ]),
        ]);
    }

    private function createFieldDefinitionCollection(): FieldDefinitionCollection
    {
        return FieldDefinitionCollection::create([
            FieldDefinition::fromAPIFieldDefinition(new ApiFieldDefinition([
                'identifier' => 'name',
            ]), null),
        ]);
    }
}

class_alias(ContentTypeCreateStepBuilderTest::class, 'Ibexa\Platform\Tests\Migration\Generator\StepBuilder\ContentTypeCreateStepBuilderTest');
