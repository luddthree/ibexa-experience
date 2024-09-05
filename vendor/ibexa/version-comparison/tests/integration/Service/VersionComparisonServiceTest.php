<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\VersionComparison\Service;

use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Test\Repository\SetupFactory\Legacy;
use Ibexa\Contracts\VersionComparison\Service\VersionComparisonServiceInterface;
use Ibexa\Contracts\VersionComparison\VersionDiff;
use Ibexa\VersionComparison\Result\DiffStatus;
use Ibexa\VersionComparison\Result\Value\Diff\TokenStringDiff;
use Ibexa\VersionComparison\Result\Value\StringComparisonResult;
use PHPUnit\Framework\TestCase;

class VersionComparisonServiceTest extends TestCase
{
    /** @var \Ibexa\Contracts\Core\Test\Repository\SetupFactory */
    private $setupFactory;

    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /** @var \Ibexa\Contracts\Core\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    private $locationService;

    /** @var \Ibexa\Contracts\VersionComparison\Service\VersionComparisonServiceInterface */
    private $versionComparisonService;

    public function setUp(): void
    {
        parent::setUp();
        $repository = $this->getSetupFactory()->getRepository();
        $this->versionComparisonService = $this->getSetupFactory()->getServiceContainer()->get(VersionComparisonServiceInterface::class);
        $this->contentService = $repository->getContentService();
        $this->contentTypeService = $repository->getContentTypeService();
        $this->locationService = $repository->getLocationService();
    }

    private function getSetupFactory()
    {
        if (null === $this->setupFactory) {
            if (false === ($setupClass = getenv('setupFactory'))) {
                $setupClass = Legacy::class;
                putenv("setupFactory={$setupClass}");
            }

            if (false === ($fixtureDir = getenv('fixtureDir'))) {
                putenv('fixtureDir=Legacy');
            }

            if (false === class_exists($setupClass)) {
                throw new \ErrorException(
                    sprintf(
                        'Environment variable "setupFactory" does not reference an existing class: %s. Did you forget to install an package dependency?',
                        $setupClass
                    )
                );
            }

            $this->setupFactory = new $setupClass();
        }

        return $this->setupFactory;
    }

    public function testContentComparisonServiceCreated()
    {
        $container = $this->getSetupFactory()->getServiceContainer();

        $this->assertInstanceOf(
            VersionComparisonServiceInterface::class,
            $container->getInnerContainer()->get(VersionComparisonServiceInterface::class)
        );
    }

    public function testCompareVersions(): void
    {
        $draft = $this->createContentDraft(
            'forum',
            2,
            [
                'name' => 'content one',
            ]
        );
        $contentA = $this->contentService->publishVersion($draft->versionInfo);

        $draftB = $this->contentService->createContentDraft($contentA->contentInfo);
        $struct = $this->contentService->newContentUpdateStruct();
        $struct->setField('name', 'content two');
        $this->contentService->updateContent($draftB->versionInfo, $struct);
        $contentB = $this->contentService->publishVersion($draftB->versionInfo);

        $versionDiff = $this->versionComparisonService->compare(
            $contentA->versionInfo,
            $contentB->versionInfo,
            'eng-US'
        );
        $this->assertInstanceOf(
            VersionDiff::class,
            $versionDiff
        );
        $fieldDiff = $versionDiff->getFieldValueDiffByIdentifier('name');
        /** @var \Ibexa\VersionComparison\Result\FieldType\TextLineComparisonResult $textCompareResult */
        $textCompareResult = $fieldDiff->getComparisonResult();
        $expectedDiff = new StringComparisonResult([
            new TokenStringDiff(DiffStatus::UNCHANGED, 'content '),
            new TokenStringDiff(DiffStatus::REMOVED, 'one'),
            new TokenStringDiff(DiffStatus::ADDED, 'two'),
        ]);
        $this->assertEquals($expectedDiff, $textCompareResult->getTextLineDiff());
    }

    public function testCompareVersionsFromDifferentContent(): void
    {
        $draftA = $this->createContentDraft(
            'forum',
            2,
            [
                'name' => 'content one',
            ]
        );
        $draftB = $this->createContentDraft(
            'forum',
            2,
            [
                'name' => 'content two',
            ]
        );

        $contentA = $this->contentService->publishVersion($draftA->versionInfo);
        $contentB = $this->contentService->publishVersion($draftB->versionInfo);
        $this->expectException(InvalidArgumentException::class);
        $this->versionComparisonService->compare($contentA->versionInfo, $contentB->versionInfo);
    }

    public function testCompareVersionsWhenFieldRemovedFromContentType(): void
    {
        $draftA = $this->createContentDraft(
            'folder',
            2,
            [
                'name' => 'content one',
                'short_name' => 'shortName content one',
            ]
        );
        $this->contentService->publishVersion($draftA->versionInfo);
        $this->removeFieldFromContentType('folder', 'short_name');

        $contentA = $this->contentService->loadContent($draftA->id, null, 1);
        $draftB = $this->contentService->createContentDraft($contentA->contentInfo);
        $struct = $this->contentService->newContentUpdateStruct();
        $struct->setField('name', 'content two');

        $this->contentService->updateContent($draftB->versionInfo, $struct);
        $contentB = $this->contentService->publishVersion($draftB->versionInfo);

        $versionDiff = $this->versionComparisonService->compare(
            $contentA->versionInfo,
            $contentB->versionInfo
        );

        $versionDiff->getFieldValueDiffByIdentifier('name');
        $this->expectException(InvalidArgumentException::class);
        $versionDiff->getFieldValueDiffByIdentifier('short_name');
    }

    public function testCompareVersionsWhenFieldAddedToContentType(): void
    {
        $draftA = $this->createContentDraft(
            'folder',
            2,
            [
                'name' => 'content one',
            ]
        );
        $this->contentService->publishVersion($draftA->versionInfo);

        $this->addFieldToContentType(
            'folder',
            'new_name',
            'ezstring'
        );

        $contentA = $this->contentService->loadContent($draftA->id, null, 1);
        $draftB = $this->contentService->createContentDraft($contentA->contentInfo);
        $struct = $this->contentService->newContentUpdateStruct();
        $struct->setField('new_name', 'content two new');
        $this->contentService->updateContent($draftB->versionInfo, $struct);
        $contentB = $this->contentService->publishVersion($draftB->versionInfo);

        $versionDiff = $this->versionComparisonService->compare(
            $contentA->versionInfo,
            $contentB->versionInfo
        );

        $fieldDiff = $versionDiff->getFieldValueDiffByIdentifier('new_name');
        /** @var \Ibexa\VersionComparison\Result\FieldType\TextLineComparisonResult $textCompareResult */
        $textCompareResult = $fieldDiff->getComparisonResult();
        $expectedDiff = new StringComparisonResult([
            new TokenStringDiff(DiffStatus::ADDED, 'content two new'),
        ]);
        $this->assertEquals($expectedDiff, $textCompareResult->getTextLineDiff());
    }

    private function addFieldToContentType(
        string $contentTypeIdentifier,
        string $fieldIdentifier,
        string $fieldTypeIdentifier
    ): void {
        $contentType = $this->contentTypeService->loadContentTypeByIdentifier($contentTypeIdentifier);
        $contentTypeDraft = $this->contentTypeService->createContentTypeDraft($contentType);
        $fieldDefCreate = $this->contentTypeService->newFieldDefinitionCreateStruct(
            $fieldIdentifier,
            $fieldTypeIdentifier
        );
        $this->contentTypeService->addFieldDefinition($contentTypeDraft, $fieldDefCreate);
        $this->contentTypeService->publishContentTypeDraft($contentTypeDraft);
    }

    private function removeFieldFromContentType(
        string $contentTypeIdentifier,
        string $fieldDefinitionIdentifier
    ): void {
        $contentType = $this->contentTypeService->loadContentTypeByIdentifier($contentTypeIdentifier);
        $contentTypeDraft = $this->contentTypeService->createContentTypeDraft($contentType);
        $this->contentTypeService->removeFieldDefinition(
            $contentTypeDraft,
            $contentType->getFieldDefinition($fieldDefinitionIdentifier)
        );
        $this->contentTypeService->publishContentTypeDraft($contentTypeDraft);
    }

    protected function createContentDraft(
        $contentTypeIdentifier,
        $parentLocationId,
        array $fieldValues
    ): Content {
        // Load content type
        $contentType = $this->contentTypeService->loadContentTypeByIdentifier($contentTypeIdentifier);

        // Prepare new Content Object
        $contentCreateStruct = $this->contentService->newContentCreateStruct($contentType, 'eng-US');

        foreach ($fieldValues as $fieldIdentifier => $fieldValue) {
            $contentCreateStruct->setField($fieldIdentifier, $fieldValue);
        }

        $contentCreateStruct->sectionId = 1;
        $contentCreateStruct->alwaysAvailable = true;

        // Prepare Location
        $locationCreateStruct = $this->locationService->newLocationCreateStruct($parentLocationId);

        // Create a draft
        $contentDraft = $this->contentService->createContent(
            $contentCreateStruct,
            [$locationCreateStruct]
        );

        return $contentDraft;
    }
}

class_alias(VersionComparisonServiceTest::class, 'EzSystems\EzPlatformVersionComparison\Integration\Tests\Service\VersionComparisonServiceTest');
