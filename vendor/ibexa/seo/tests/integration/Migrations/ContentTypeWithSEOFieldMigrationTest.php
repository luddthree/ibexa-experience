<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Seo\Migrations;

use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\Generator\StepBuilder\ContentTypeStepFactory;
use Ibexa\Seo\Value\SeoTypesValue;
use Ibexa\Seo\Value\SeoTypeValue;

final class ContentTypeWithSEOFieldMigrationTest extends BaseSEOTypeMigrationTestCase
{
    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\Exception
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function testExportingContentTypeWithSEOField(): void
    {
        $ibexaTestCore = $this->getIbexaTestCore();
        $contentTypeStepFactory = $ibexaTestCore->getServiceByClassName(ContentTypeStepFactory::class);

        $contentType = $this->createContentTypeWithSEOFieldDefinition();
        $contentTypeStep = $contentTypeStepFactory->create($contentType, new Mode(Mode::CREATE));

        self::assertEquals(
            $this->getMigrationSnapshot('output_content_type_with_seo_field.yaml'),
            $this->migrationExportToYaml($contentTypeStep)
        );
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function testImportingContentTypeWithSEOField(): void
    {
        $migrationFileName = 'input_content_type_with_seo_field.yaml';
        $ibexaTestCore = $this->getIbexaTestCore();
        $contentTypeService = $ibexaTestCore->getContentTypeService();

        $this->migrationImportFromYaml($migrationFileName);

        $contentType = $contentTypeService->loadContentTypeByIdentifier(self::CONTENT_TYPE_IDENTIFIER);
        $seoFieldDefinition = $contentType->getFieldDefinition(self::SEO_FIELD_DEFINITION_IDENTIFIER);
        self::assertNotNull($seoFieldDefinition);
        self::assertEquals(
            [
                'types' => new SeoTypesValue(
                    [
                        'foo' => new SeoTypeValue('foo', ['field1' => 'value1']),
                    ]
                ),
            ],
            $seoFieldDefinition->getFieldSettings()
        );
    }
}
