<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Migration\Generator\StepBuilder;

use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\ObjectStateService;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Field as APIField;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\Content\Section;
use Ibexa\Core\FieldType;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\Content\Location as APILocation;
use Ibexa\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Core\Repository\Values\ObjectState\ObjectState;
use Ibexa\Core\Repository\Values\ObjectState\ObjectStateGroup;
use Ibexa\Migration\Generator\Content\StepBuilder\Create;
use Ibexa\Migration\Generator\Reference\ContentGenerator as ReferenceContentGenerator;
use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Ibexa\Migration\Service\FieldTypeServiceInterface;
use Ibexa\Migration\ValueObject\Content\CreateMetadata;
use Ibexa\Migration\ValueObject\Content\Field;
use Ibexa\Migration\ValueObject\Content\Location;
use Ibexa\Migration\ValueObject\Content\ObjectState as ObjectStateVO;
use Ibexa\Migration\ValueObject\Step\Action\Content\AssignObjectState;
use Ibexa\Migration\ValueObject\Step\ContentCreateStep;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class ContentCreateStepBuilderTest extends TestCase
{
    /** @var \Ibexa\Migration\Generator\Content\StepBuilder\Create */
    private $builder;

    /** @var \Ibexa\Core\Repository\Values\Content\Location */
    private $mainLocation;

    /** @var \Ibexa\Core\Repository\Values\Content\Location */
    private $parentLocation;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Field */
    private $field;

    protected function setUp(): void
    {
        $this->parentLocation = new APILocation([
            'parentLocationId' => null,
            'remoteId' => '__remote_id__',
        ]);

        $this->mainLocation = new APILocation([
            'parentLocationId' => 1,
            'parentLocation' => $this->parentLocation,
        ]);

        $this->field = new APIField([
            'fieldDefIdentifier' => 'name',
            'fieldTypeIdentifier' => 'ezstring',
            'languageCode' => 'eng-GB',
            'id' => 186,
            'value' => new FieldType\TextLine\Value('Home'),
        ]);

        $fieldTypeService = $this->createMock(FieldTypeServiceInterface::class);
        $objectStateService = $this->createMock(ObjectStateService::class);

        $objectStateService
            ->method('loadObjectStateGroups')
            ->willReturn([]);

        $this->builder = new Create(
            new ReferenceContentGenerator(),
            $fieldTypeService,
            $objectStateService
        );
    }

    public function testBuild(): void
    {
        $content = $this->createContent();

        $result = $this->builder->build($content);

        /** @var \Ibexa\Migration\ValueObject\Content\Field[] $fields */
        $fields = [];
        foreach ($content->getFields() as $key => $contentField) {
            $fields[$key] = Field::createFromValueObject($contentField, null);
        }

        $expectedContentCreateStep = new ContentCreateStep(
            CreateMetadata::createFromContent($content),
            Location::createFromValueObject($this->mainLocation),
            $fields,
            [
                new ReferenceDefinition('ref__content__landing_page__home', 'content_id'),
                new ReferenceDefinition('ref_location__landing_page__home', 'location_id'),
                new ReferenceDefinition('ref_path__landing_page__home', 'path'),
            ]
        );

        Assert::assertEquals($expectedContentCreateStep, $result);
    }

    public function testBuildWithActions(): void
    {
        $fieldTypeService = $this->createMock(FieldTypeServiceInterface::class);
        $objectStateService = $this->createMock(ObjectStateService::class);
        $locationService = $this->createMock(LocationService::class);

        $content = $this->createContent();

        $objectStateGroup1 = new ObjectStateGroup([
            'identifier' => 'state_group_1',
            'mainLanguageCode' => 'eng-GB',
            'names' => ['eng-GB' => 'Object State Group 1 name'],
            'descriptions' => ['eng-GB' => 'Object State Group 1 description'],
        ]);

        $objectStateGroup2 = new ObjectStateGroup([
            'identifier' => 'state_group_2',
            'mainLanguageCode' => 'eng-GB',
            'names' => ['eng-GB' => 'Object State Group 2 name'],
            'descriptions' => ['eng-GB' => 'Object State Group 2 description'],
        ]);

        $objectState1Group1 = new ObjectState([
            'identifier' => 'state_1',
            'mainLanguageCode' => 'eng-GB',
            'objectStateGroup' => $objectStateGroup1,
            'names' => ['eng-GB' => 'Object State 1 name'],
            'descriptions' => ['eng-GB' => 'Object State 1 description'],
        ]);

        $objectState2Group1 = new ObjectState([
            'identifier' => 'state_2',
            'mainLanguageCode' => 'eng-GB',
            'objectStateGroup' => $objectStateGroup1,
            'names' => ['eng-GB' => 'Object State 2 name'],
            'descriptions' => ['eng-GB' => 'Object State 2 description'],
        ]);

        $objectState3Group2 = new ObjectState([
            'identifier' => 'state_3',
            'mainLanguageCode' => 'eng-GB',
            'objectStateGroup' => $objectStateGroup2,
            'names' => ['eng-GB' => 'Object State 3 name'],
            'descriptions' => ['eng-GB' => 'Object State 3 description'],
        ]);

        $objectState4Group2 = new ObjectState([
            'identifier' => 'state_4',
            'mainLanguageCode' => 'eng-GB',
            'objectStateGroup' => $objectStateGroup2,
            'names' => ['eng-GB' => 'Object State 4 name'],
            'descriptions' => ['eng-GB' => 'Object State 4 description'],
        ]);

        $objectStateService->method(
            'loadObjectStateGroups'
        )->willReturn(
            [$objectStateGroup1, $objectStateGroup2]
        );

        $objectStateService->method(
            'loadObjectStates'
        )->withConsecutive(
            [$objectStateGroup1, []],
            [$objectStateGroup2, []],
        )->willReturnOnConsecutiveCalls(
            [$objectState1Group1, $objectState2Group1],
            [$objectState3Group2, $objectState4Group2],
        );

        $objectStateService->method(
            'getContentState'
        )->withConsecutive(
            [$content->contentInfo, $objectStateGroup1],
            [$content->contentInfo, $objectStateGroup2]
        )->willReturnOnConsecutiveCalls(
            $objectState2Group1,
            $objectState3Group2,
        );

        $locationService
            ->method('loadLocation')
            ->willReturn($this->parentLocation);

        $builder = new Create(
            new ReferenceContentGenerator(),
            $fieldTypeService,
            $objectStateService
        );

        $result = $builder->build($content);

        /** @var \Ibexa\Migration\ValueObject\Content\Field[] $fields */
        $fields = [];
        foreach ($content->getFields() as $key => $contentField) {
            $fields[$key] = Field::createFromValueObject($contentField, null);
        }

        $expectedContentCreateStep = new ContentCreateStep(
            CreateMetadata::createFromContent($content),
            Location::createFromValueObject($this->mainLocation),
            $fields,
            [
                new ReferenceDefinition('ref__content__landing_page__home', 'content_id'),
                new ReferenceDefinition('ref_location__landing_page__home', 'location_id'),
                new ReferenceDefinition('ref_path__landing_page__home', 'path'),
            ],
            [
                new AssignObjectState(
                    ObjectStateVO::createFromValueObject(
                        $objectState2Group1
                    )
                ),
            ]
        );

        Assert::assertEquals($expectedContentCreateStep, $result);
    }

    private function createContent(): Content
    {
        return new Content([
            'versionInfo' => new VersionInfo([
                'names' => [
                    'eng-GB' => 'Home',
                ],
                'initialLanguageCode' => 'eng-GB',
                'contentInfo' => new ContentInfo([
                    'remoteId' => '8a9c9c761004866fb458d89910f52bee',
                    'mainLanguage' => new Language([
                        'languageCode' => 'eng-GB',
                    ]),
                    'ownerId' => 14,
                    'alwaysAvailable' => true,
                    'sectionId' => 1,
                    'section' => new Section([
                        'id' => 1,
                        'identifier' => 'foo_section_identifier',
                    ]),
                    'modificationDate' => new \DateTime('2007-11-28T16:51:36.000000'),
                    'publishedDate' => new \DateTime('2007-11-19T13:54:46.000000'),
                    'mainLocation' => $this->mainLocation,
                    'contentType' => new ContentType([
                        'identifier' => 'landing_page',
                    ]),
                ]),
            ]),
            'contentType' => new ContentType([
                'identifier' => 'landing_page',
            ]),
            'internalFields' => [
                $this->field,
            ],
        ]);
    }
}

class_alias(ContentCreateStepBuilderTest::class, 'Ibexa\Platform\Tests\Migration\Generator\StepBuilder\ContentCreateStepBuilderTest');
