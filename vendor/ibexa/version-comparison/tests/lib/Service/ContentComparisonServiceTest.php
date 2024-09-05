<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\VersionComparison\Service;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\VersionComparison\FieldValueDiff;
use Ibexa\Contracts\VersionComparison\Service\VersionComparisonServiceInterface;
use Ibexa\Contracts\VersionComparison\VersionDiff;
use Ibexa\Core\FieldType\TextLine\Value as TextLineValue;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\Content\VersionInfo as CoreVersionInfo;
use Ibexa\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\Repository\Values\ContentType\FieldDefinitionCollection;
use Ibexa\Tests\Core\Repository\Service\Mock\Base;
use Ibexa\VersionComparison\Engine\FieldType\TextLineComparisonEngine;
use Ibexa\VersionComparison\Engine\Value\StringComparisonEngine;
use Ibexa\VersionComparison\FieldType\TextLine\Comparable as TextLineComparableFieldType;
use Ibexa\VersionComparison\FieldType\TextLine\Value;
use Ibexa\VersionComparison\Registry\ComparisonEngineRegistry;
use Ibexa\VersionComparison\Registry\ComparisonEngineRegistryInterface;
use Ibexa\VersionComparison\Registry\FieldRegistry;
use Ibexa\VersionComparison\Registry\FieldRegistryInterface;
use Ibexa\VersionComparison\Result\DiffStatus;
use Ibexa\VersionComparison\Result\FieldType\TextLineComparisonResult;
use Ibexa\VersionComparison\Result\Value\Diff\TokenStringDiff;
use Ibexa\VersionComparison\Result\Value\StringComparisonResult;
use Ibexa\VersionComparison\Service\VersionComparisonService;

class ContentComparisonServiceTest extends Base
{
    /** @var \Ibexa\VersionComparison\Registry\FieldRegistryInterface */
    private $fieldRegistry;

    /** @var \Ibexa\VersionComparison\Registry\ComparisonEngineRegistryInterface */
    private $comparisonEngineRegistry;

    /** @var \Ibexa\Contracts\Core\Repository\ContentTypeService|\PHPUnit\Framework\MockObject\MockObject */
    private $contentTypeServiceMock;

    /** @var \Ibexa\Contracts\Core\Repository\ContentService|\PHPUnit\Framework\MockObject\MockObject */
    private $contentServiceMock;

    public function setUp(): void
    {
        $this->contentTypeServiceMock = $this->buildContentTypeServiceMock();
        $this->contentServiceMock = $this->buildContentServiceMock();
        $this->fieldRegistry = $this->buildFieldRegistry();
        $this->fieldRegistry->registerType('ezstring', new TextLineComparableFieldType());

        $this->comparisonEngineRegistry = $this->buildComparisonEngineRegistry();
        $this->comparisonEngineRegistry->registerEngine(
            Value::class,
            new TextLineComparisonEngine(
                new StringComparisonEngine()
            )
        );

        parent::setUp();
    }

    /**
     * @return \Ibexa\Contracts\VersionComparison\Service\VersionComparisonServiceInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function createContentComparisonService(): VersionComparisonServiceInterface
    {
        $permissionResolverMock = $this->getPermissionResolverMock();
        $permissionResolverMock
            ->method('hasAccess')
            ->willReturn(true);

        return $this
            ->getMockBuilder(VersionComparisonServiceInterface::class)
            ->enableProxyingToOriginalMethods()
            ->onlyMethods(['compare'])
            ->setProxyTarget(
                new VersionComparisonService(
                    $this->fieldRegistry,
                    $this->comparisonEngineRegistry,
                    $this->contentServiceMock,
                    $this->contentTypeServiceMock,
                    $permissionResolverMock
                )
            )
            ->getMock();
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\ContentService|\PHPUnit\Framework\MockObject\MockObject
     */
    private function buildContentServiceMock(): ContentService
    {
        return $this
            ->getMockBuilder(ContentService::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\ContentTypeService|\PHPUnit\Framework\MockObject\MockObject
     */
    private function buildContentTypeServiceMock(): ContentTypeService
    {
        return $this
            ->getMockBuilder(ContentTypeService::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function buildFieldRegistry(): FieldRegistryInterface
    {
        return new FieldRegistry();
    }

    private function buildComparisonEngineRegistry(): ComparisonEngineRegistryInterface
    {
        return new ComparisonEngineRegistry();
    }

    public function testCompareTwoVersions(): void
    {
        $versionOne = $this->getVersionInfo(77, 1);
        $versionTwo = $this->getVersionInfo(77, 2);

        $fieldDefinition = new FieldDefinition([
            'fieldTypeIdentifier' => 'ezstring',
            'identifier' => 'textDefId',
        ]);

        $contentType = new ContentType([
            'fieldDefinitions' => new FieldDefinitionCollection([
                $fieldDefinition,
            ]),
        ]);

        $contentOne = new Content([
            'internalFields' => [
                new Field([
                    'id' => 3,
                    'languageCode' => 'eng-GB',
                    'fieldDefIdentifier' => 'textDefId',
                    'fieldTypeIdentifier' => 'ezstring',
                    'value' => new TextLineValue('We love the Big Apple'),
                ]),
            ],
            'contentType' => $contentType,
        ]);

        $contentTwo = new Content([
            'internalFields' => [
                new Field([
                    'id' => 3,
                    'languageCode' => 'eng-GB',
                    'fieldDefIdentifier' => 'textDefId',
                    'fieldTypeIdentifier' => 'ezstring',
                    'value' => new TextLineValue('We love NY'),
                ]),
            ],
            'contentType' => $contentType,
        ]);

        $this->contentServiceMock
            ->expects($this->exactly(2))
            ->method('loadContent')
            ->withConsecutive(
                [77, ['eng-GB'], 1],
                [77, ['eng-GB'], 2]
            )->willReturnOnConsecutiveCalls(
                $contentOne,
                $contentTwo
            );

        $service = $this->createContentComparisonService();

        $versionDiff = $service->compare($versionOne, $versionTwo);

        $this->assertInstanceOf(
            VersionDiff::class,
            $versionDiff
        );

        $diffValue = new TextLineComparisonResult(new StringComparisonResult([
            new TokenStringDiff(DiffStatus::UNCHANGED, 'We love '),
            new TokenStringDiff(DiffStatus::REMOVED, 'the Big Apple'),
            new TokenStringDiff(DiffStatus::ADDED, 'NY'),
        ]));

        $expectedFieldDiff = new FieldValueDiff(
            $fieldDefinition,
            $diffValue
        );

        $expectedVersionDiff = new VersionDiff([
            'textDefId' => $expectedFieldDiff,
        ]);

        $this->assertEquals(
            $expectedVersionDiff,
            $versionDiff
        );
    }

    protected function getVersionInfo(int $id, int $versionNo): VersionInfo
    {
        return new CoreVersionInfo([
            'contentInfo' => new ContentInfo(['id' => $id]),
            'versionNo' => $versionNo,
            'initialLanguageCode' => 'eng-GB',
            'languageCodes' => ['eng-GB', 'eng-US'],
        ]);
    }
}

class_alias(ContentComparisonServiceTest::class, 'EzSystems\EzPlatformVersionComparison\Tests\Service\ContentComparisonServiceTest');
