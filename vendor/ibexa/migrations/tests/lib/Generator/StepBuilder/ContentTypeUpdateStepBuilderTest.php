<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Migration\Generator\StepBuilder;

use DateTime;
use Ibexa\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Core\Repository\Values\ContentType\FieldDefinition as ApiFieldDefinition;
use Ibexa\Core\Repository\Values\ContentType\FieldDefinitionCollection as ApiFieldDefinitionCollection;
use Ibexa\Migration\Generator\StepBuilder\ContentTypeUpdateStepBuilder;
use Ibexa\Migration\ValueObject\ContentType\FieldDefinition;
use Ibexa\Migration\ValueObject\ContentType\FieldDefinitionCollection;
use Ibexa\Migration\ValueObject\ContentType\Matcher;
use Ibexa\Migration\ValueObject\ContentType\UpdateMetadata;
use Ibexa\Migration\ValueObject\Step\ContentTypeUpdateStep;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class ContentTypeUpdateStepBuilderTest extends TestCase
{
    /** @var \Ibexa\Migration\Generator\StepBuilder\ContentTypeUpdateStepBuilder */
    private $builder;

    protected function setUp(): void
    {
        $this->builder = new ContentTypeUpdateStepBuilder();
    }

    public function testBuild(): void
    {
        $contentType = $this->createContentType();

        $result = $this->builder->build($contentType);

        $expectedContentTypeCreateStep = new ContentTypeUpdateStep(
            UpdateMetadata::create($contentType),
            $this->createFieldDefinitionCollection(),
            new Matcher(
                Matcher::CONTENT_TYPE_IDENTIFIER,
                $contentType->identifier
            ),
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
                1,
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

class_alias(ContentTypeUpdateStepBuilderTest::class, 'Ibexa\Platform\Tests\Migration\Generator\StepBuilder\ContentTypeUpdateStepBuilderTest');
