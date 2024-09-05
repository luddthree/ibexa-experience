<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Measurement\Repository;

use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeCreateStruct;
use Ibexa\Contracts\Core\Test\IbexaKernelTestCase;

abstract class BaseIntegrationTestCase extends IbexaKernelTestCase
{
    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    protected function setUp(): void
    {
        self::bootKernel();

        self::setAdministratorUser();
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ForbiddenException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    protected function createMeasurementContentType(): ContentType
    {
        $contentTypeService = self::getContentTypeService();
        $contentTypeContentGroup = $contentTypeService->loadContentTypeGroupByIdentifier('Content');
        $contentTypeCreateStruct = $contentTypeService->newContentTypeCreateStruct(
            'measurement_ct'
        );
        $contentTypeCreateStruct->names = ['eng-US' => 'Measurement'];
        $contentTypeCreateStruct->mainLanguageCode = 'eng-US';
        $this->addFieldDefinition(
            'name',
            'ezstring',
            $contentTypeService,
            $contentTypeCreateStruct
        );
        $this->addFieldDefinition(
            'measurement',
            'ibexa_measurement',
            $contentTypeService,
            $contentTypeCreateStruct
        );
        $contentType = $contentTypeService->createContentType(
            $contentTypeCreateStruct,
            [$contentTypeContentGroup]
        );
        $contentTypeService->publishContentTypeDraft($contentType);

        return $contentType;
    }

    protected function addFieldDefinition(
        string $identifier,
        string $fieldTypeIdentifier,
        ContentTypeService $contentTypeService,
        ContentTypeCreateStruct $contentTypeCreateStruct
    ): void {
        $fieldDefinitionCreateStruct = $contentTypeService->newFieldDefinitionCreateStruct(
            $identifier,
            $fieldTypeIdentifier
        );
        $fieldDefinitionCreateStruct->position = count(
            $contentTypeCreateStruct->fieldDefinitions
        ) + 1;
        $contentTypeCreateStruct->addFieldDefinition($fieldDefinitionCreateStruct);
    }
}
