<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Seo\Migrations;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Migration\Generator\Content\StepBuilder\Create as ContentCreateStepBuilder;
use Ibexa\Seo\FieldType\SeoValue;
use Ibexa\Seo\Value\SeoTypesValue;
use Ibexa\Seo\Value\SeoTypeValue;

final class ContentWithSEOFieldMigrationTest extends BaseSEOTypeMigrationTestCase
{
    private const ENG_GB = 'eng-GB';

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\Exception
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function testExportingContentWithSEOField(): void
    {
        $ibexaTestCore = $this->getIbexaTestCore();
        $contentCreateStepBuilder = $ibexaTestCore->getServiceByClassName(ContentCreateStepBuilder::class);

        $contentType = $this->createContentTypeWithSEOFieldDefinition();
        $contentItem = $this->createContentItemWithSEOField($contentType);

        $contentCreateStep = $contentCreateStepBuilder->build($contentItem);

        self::assertEquals(
            $this->getMigrationSnapshot('output_content_with_seo_field.yaml'),
            $this->migrationExportToYaml($contentCreateStep)
        );
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\Exception
     */
    public function testImportingContentWithSEOField(): void
    {
        $migrationFileName = 'input_content_with_seo_field.yaml';
        $ibexaTestCore = $this->getIbexaTestCore();
        $contentService = $ibexaTestCore->getContentService();

        $this->createContentTypeWithSEOFieldDefinition();
        $this->migrationImportFromYaml($migrationFileName);

        $content = $contentService->loadContentByRemoteId('my_seo_content_remote_id');
        $field = $content->getField('seo_field');
        self::assertNotNull($field);
        self::assertEquals(
            new SeoValue(
                new SeoTypesValue(
                    [
                        'foo' => new SeoTypeValue('foo', ['field1' => 'Field 1 content value']),
                    ]
                )
            ),
            $field->getValue()
        );
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\Exception
     */
    private function createContentItemWithSEOField(ContentType $seoContentType): Content
    {
        $ibexaTestCore = $this->getIbexaTestCore();
        $contentService = $ibexaTestCore->getContentService();
        $locationService = $ibexaTestCore->getLocationService();

        $contentCreateStruct = $contentService->newContentCreateStruct($seoContentType, self::ENG_GB);
        $contentCreateStruct->setField(
            self::SEO_FIELD_DEFINITION_IDENTIFIER,
            new SeoValue(
                new SeoTypesValue(
                    [
                        new SeoTypeValue('foo', ['field1' => 'Field 1 content value']),
                    ]
                )
            )
        );

        $contentDraft = $contentService->createContent(
            $contentCreateStruct,
            [$locationService->newLocationCreateStruct(self::CONTENT_ROOT_LOCATION_ID)]
        );

        return $contentService->publishVersion($contentDraft->getVersionInfo());
    }
}
