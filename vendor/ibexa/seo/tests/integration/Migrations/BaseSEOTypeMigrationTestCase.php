<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Seo\Migrations;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Migration\Metadata\Storage\MetadataStorage;
use Ibexa\Contracts\Migration\MigrationService;
use Ibexa\Contracts\Test\Core\IbexaKernelTestCase;
use Ibexa\Migration\Repository\Migration;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Seo\FieldType\SeoType;
use Ibexa\Seo\Value\SeoTypesValue;
use Ibexa\Seo\Value\SeoTypeValue;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

abstract class BaseSEOTypeMigrationTestCase extends IbexaKernelTestCase
{
    protected const CONTENT_TYPE_IDENTIFIER = 'seo_content_type';
    protected const CONTENT_TYPE_GROUP_IDENTIFIER = 'Content';

    protected const CONTENT_ROOT_LOCATION_ID = 2;

    public const SEO_FIELD_DEFINITION_IDENTIFIER = 'seo_field';

    protected function setUp(): void
    {
        $this->getIbexaTestCore()->setAdministratorUser();
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\Exception
     */
    protected function createContentTypeWithSEOFieldDefinition(): ContentType
    {
        $contentTypeService = $this->getIbexaTestCore()->getContentTypeService();

        $contentTypeContentGroup = $contentTypeService->loadContentTypeGroupByIdentifier(
            self::CONTENT_TYPE_GROUP_IDENTIFIER
        );

        $contentTypeCreateStruct = $contentTypeService->newContentTypeCreateStruct(self::CONTENT_TYPE_IDENTIFIER);
        $contentTypeCreateStruct->mainLanguageCode = 'eng-GB';
        $contentTypeCreateStruct->names = ['eng-GB' => 'SEO content type'];
        $fieldDefinition = $contentTypeService->newFieldDefinitionCreateStruct(
            self::SEO_FIELD_DEFINITION_IDENTIFIER,
            SeoType::IDENTIFIER
        );
        $fieldDefinition->fieldSettings = ['types' => new SeoTypesValue(
            [
                new SeoTypeValue(
                    'foo',
                    ['field1' => 'value1']
                ),
            ]
        )];
        $contentTypeCreateStruct->addFieldDefinition($fieldDefinition);

        $contentTypeService->publishContentTypeDraft(
            $contentTypeService->createContentType($contentTypeCreateStruct, [$contentTypeContentGroup])
        );

        return $contentTypeService->loadContentTypeByIdentifier(self::CONTENT_TYPE_IDENTIFIER);
    }

    /**
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    protected function migrationExportToYaml(StepInterface $step): string
    {
        $ibexaTestCore = $this->getIbexaTestCore();
        $normalizer = $ibexaTestCore->getServiceByClassName(NormalizerInterface::class, 'ibexa.migrations.serializer');
        $encoder = $ibexaTestCore->getServiceByClassName(EncoderInterface::class, 'ibexa.migrations.serializer');

        $format = 'yaml';
        $context = ['yaml_inline' => 6, 'yaml_indent' => 4];
        $encodedData = $encoder->encode(
            [
                $normalizer->normalize($step, $format, $context),
            ],
            $format,
            $context
        );

        return self::replaceDynamicData($encodedData);
    }

    protected function migrationImportFromYaml(string $migrationFileName): void
    {
        $ibexaTestCore = $this->getIbexaTestCore();
        $migrationService = $ibexaTestCore->getServiceByClassName(MigrationService::class);
        $migrationMetadataStorage = $ibexaTestCore->getServiceByClassName(MetadataStorage::class);

        $migrationMetadataStorage->ensureInitialized();
        $migrationMetadataStorage->reset();
        $snapshot = $this->getMigrationSnapshot($migrationFileName);
        $migration = new Migration($migrationFileName, $snapshot);
        $migrationService->executeOne($migration);
    }

    protected static function replaceDynamicData(string $contents): string
    {
        $result = preg_replace('/([a-z]+Date|[a-z]*[rR]emoteId): .*/m', '\1: __DYNAMIC__', $contents);
        self::assertNotNull($result, "Failed to replace dynamic data in the contents: '$contents'");

        return $result;
    }

    protected function getMigrationSnapshot(string $fileName): string
    {
        $contents = file_get_contents(self::getMigrationSnapshotFilePath($fileName));
        self::assertNotFalse($contents, "Unable to load migration snapshot '$fileName'");

        return $contents;
    }

    protected static function getMigrationSnapshotFilePath(string $fileName): string
    {
        $filePath = dirname(__DIR__) . "/Resources/Migrations/snapshots/$fileName";
        self::assertFileExists($filePath);

        return $filePath;
    }
}
