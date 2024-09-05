<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\PageBuilder;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinitionCreateStruct;
use Ibexa\Contracts\Core\Test\IbexaKernelTestCase;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page;
use Ibexa\FieldTypePage\FieldType\LandingPage\Value;

abstract class BasePageBuilderIntegrationTestCase extends IbexaKernelTestCase
{
    protected const FIELD_DEFINITION_IDENTIFIER = 'page';
    protected const DEFAULT_LANGUAGE_CODE = 'eng-US';

    protected ContentService $contentService;

    protected ContentTypeService $contentTypeService;

    protected function setUp(): void
    {
        $this->contentService = self::getServiceByClassName(ContentService::class);
        $this->contentTypeService = self::getServiceByClassName(ContentTypeService::class);
    }

    protected function createLandingPageContentType(): ContentType
    {
        $createStruct = $this->contentTypeService->newContentTypeCreateStruct('landing_page_test');
        $createStruct->mainLanguageCode = self::DEFAULT_LANGUAGE_CODE;
        $createStruct->names = [$createStruct->mainLanguageCode => 'Landing page'];

        $createStruct->addFieldDefinition($this->createLandingPageFieldDefinitionCreateStruct());

        $contentGroup = $this->contentTypeService->loadContentTypeGroupByIdentifier('Content');
        $contentTypeDraft = $this->contentTypeService->createContentType(
            $createStruct,
            [$contentGroup]
        );

        $this->contentTypeService->publishContentTypeDraft($contentTypeDraft);

        return $this->contentTypeService->loadContentType($contentTypeDraft->id);
    }

    private function createLandingPageFieldDefinitionCreateStruct(): FieldDefinitionCreateStruct
    {
        $fieldCreate = $this->contentTypeService->newFieldDefinitionCreateStruct(
            'page',
            'ezlandingpage'
        );
        $fieldCreate->names = [self::DEFAULT_LANGUAGE_CODE => 'Landing page'];
        $fieldCreate->fieldGroup = 'main';
        $fieldCreate->position = 1;
        $fieldCreate->isTranslatable = true;

        return $fieldCreate;
    }

    /**
     * @param array<\Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Zone> $zones
     */
    protected static function assertPageZones(array $zones, Content $landingPage): void
    {
        $field = $landingPage->getField(self::FIELD_DEFINITION_IDENTIFIER);
        self::assertNotNull($field, 'Missing field with identifier: ' . self::FIELD_DEFINITION_IDENTIFIER);

        $value = $field->getValue();
        self::assertInstanceOf(Value::class, $value);

        $page = $value->getPage();
        self::assertInstanceOf(Page::class, $page);

        self::assertEquals($zones, $page->getZones());
    }
}
