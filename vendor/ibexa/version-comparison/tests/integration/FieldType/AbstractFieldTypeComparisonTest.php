<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\VersionComparison\FieldType;

use ErrorException;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinitionCreateStruct;
use Ibexa\Contracts\Core\Test\Repository\SetupFactory\Legacy;
use Ibexa\Contracts\VersionComparison\Result\ComparisonResult;
use Ibexa\Contracts\VersionComparison\Service\VersionComparisonServiceInterface;
use PHPUnit\Framework\TestCase;

abstract class AbstractFieldTypeComparisonTest extends TestCase
{
    /** @var \Ibexa\Contracts\Core\Test\Repository\SetupFactory */
    protected $setupFactory;

    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    protected $contentService;

    /** @var \Ibexa\Contracts\Core\Repository\ContentTypeService */
    protected $contentTypeService;

    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    protected $locationService;

    /** @var \Ibexa\Contracts\VersionComparison\Service\VersionComparisonServiceInterface */
    protected $versionComparisonService;

    abstract protected function getFieldType(): string;

    abstract public function dataToCompare(): array;

    public function setUp(): void
    {
        parent::setUp();
        $repository = $this->getSetupFactory()->getRepository();
        $this->versionComparisonService = $this->getSetupFactory()->getServiceContainer()->get(VersionComparisonServiceInterface::class);
        $this->contentService = $repository->getContentService();
        $this->contentTypeService = $repository->getContentTypeService();
        $this->locationService = $repository->getLocationService();
    }

    protected function getSetupFactory()
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
                throw new ErrorException(
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

    /**
     * @dataProvider dataToCompare
     */
    public function testCompareVersions($oldValue, $newValue, ComparisonResult $expectedComparisonResult): void
    {
        $contentA = $this->createContentA($oldValue);
        $contentB = $this->createContentB($contentA, $newValue);

        $versionDiff = $this->versionComparisonService->compare(
            $contentA->versionInfo,
            $contentB->versionInfo,
            'eng-US'
        );

        $fieldDiff = $versionDiff->getFieldValueDiffByIdentifier($this->getFieldType());

        $this->assertComparisonResult($expectedComparisonResult, $fieldDiff->getComparisonResult());
    }

    protected function assertComparisonResult(
        ComparisonResult $expected,
        ComparisonResult $actual
    ): void {
        $this->assertEquals(
            $expected,
            $actual
        );
    }

    protected function createContentType(
        string $contentTypeId,
        string $fieldType
    ): void {
        $typeCreate = $this->contentTypeService->newContentTypeCreateStruct($contentTypeId);
        $typeCreate->mainLanguageCode = 'eng-GB';
        $typeCreate->names = [
            'eng-GB' => $contentTypeId,
        ];

        $fieldDefinitionCreateStruct = $this->buildFieldDefinitionCreateStruct($fieldType);
        $typeCreate->addFieldDefinition($fieldDefinitionCreateStruct);

        $contentTypeDraft = $this->contentTypeService->createContentType(
            $typeCreate,
            [$this->contentTypeService->loadContentTypeGroupByIdentifier('Content')]
        );

        $this->contentTypeService->publishContentTypeDraft($contentTypeDraft);
    }

    protected function buildFieldDefinitionCreateStruct(string $fieldType): FieldDefinitionCreateStruct
    {
        $titleFieldCreate = $this->contentTypeService->newFieldDefinitionCreateStruct($fieldType, $fieldType);
        $titleFieldCreate->names = [
            'eng-GB' => $fieldType,
        ];

        return $titleFieldCreate;
    }

    protected function createContentDraft(
        string $contentTypeIdentifier,
        int $parentLocationId,
        array $fieldValues,
        ?string $remoteId = null
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
        $contentCreateStruct->remoteId = $remoteId;

        // Prepare Location
        $locationCreateStruct = $this->locationService->newLocationCreateStruct($parentLocationId);

        // Create a draft
        return $this->contentService->createContent(
            $contentCreateStruct,
            [$locationCreateStruct]
        );
    }

    /**
     * @param mixed|null $value
     */
    protected function createContentA($value): Content
    {
        $fieldIdentifier = $this->getFieldType();

        $this->createContentType(
            'content_type_' . $fieldIdentifier,
            $fieldIdentifier
        );

        $draft = $this->createContentDraft(
            'content_type_' . $fieldIdentifier,
            2,
            [
                $fieldIdentifier => $value,
            ]
        );

        return $this->contentService->publishVersion($draft->versionInfo);
    }

    /**
     * @param mixed|null $value
     */
    protected function createContentB(Content $content, $value): Content
    {
        $draftB = $this->contentService->createContentDraft($content->contentInfo);
        $struct = $this->contentService->newContentUpdateStruct();
        $struct->setField($this->getFieldType(), $value);
        $this->contentService->updateContent($draftB->versionInfo, $struct);

        return $this->contentService->publishVersion($draftB->versionInfo);
    }
}

class_alias(AbstractFieldTypeComparisonTest::class, 'EzSystems\EzPlatformVersionComparison\Integration\Tests\FieldType\AbstractFieldTypeComparisonTest');
