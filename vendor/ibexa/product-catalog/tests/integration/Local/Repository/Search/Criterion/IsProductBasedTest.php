<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository\Search\Criterion;

use DateTime;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\IsProductBased;
use Ibexa\ProductCatalog\FieldType\ProductSpecification\Type;
use Ibexa\ProductCatalog\FieldType\ProductSpecification\Value;
use Ibexa\Tests\Integration\ProductCatalog\IbexaKernelTestCase;
use Ibexa\Tests\Integration\ProductCatalog\Local\Repository\ProductDatabaseAssertionTrait;

final class IsProductBasedTest extends IbexaKernelTestCase
{
    use ProductDatabaseAssertionTrait;

    protected function setUp(): void
    {
        self::bootKernel();
    }

    public function testIsProductBasedCriterion(): void
    {
        self::getPermissionResolver()->setCurrentUserReference(
            self::getUserService()->loadUserByLogin('admin')
        );

        $searchService = self::getSearchService();

        $query = new Query();
        $query->filter = new IsProductBased();

        self::ensureSearchIndexIsUpdated();
        $count = self::getContentProductsCount();
        self::assertEquals($count, $searchService->findContent($query)->totalCount);

        $contentType = $this->createContentTypeWithProductSpecificationField('product_example');

        $this->createContentWithProductSpecification($contentType, 'Product A', '0001');
        $this->createContentWithProductSpecification($contentType, 'Product B', '0002');
        $this->createContentWithProductSpecification($contentType, 'Product C', '0003');

        self::ensureSearchIndexIsUpdated();
        self::assertEquals($count + 3, $searchService->findContent($query)->totalCount);
    }

    private function createContentWithProductSpecification(ContentType $contentType, string $name, string $code): void
    {
        $contentService = self::getContentService();

        $createStruct = $contentService->newContentCreateStruct($contentType, 'eng-GB');
        $createStruct->setField('name', $name);
        $createStruct->setField('product', new Value(null, $code));

        $contentService->publishVersion(
            $contentService->createContent($createStruct)->getVersionInfo()
        );
    }

    private function createContentTypeWithProductSpecificationField(string $identifier): ContentType
    {
        $contentTypeService = self::getContentTypeService();

        $typeCreate = $contentTypeService->newContentTypeCreateStruct($identifier);
        $typeCreate->mainLanguageCode = 'eng-GB';
        $typeCreate->urlAliasSchema = '<name>';
        $typeCreate->nameSchema = '<name>';
        $typeCreate->names = ['eng-GB' => 'Product: ' . $identifier];
        $typeCreate->creatorId = self::getPermissionResolver()->getCurrentUserReference()->getUserId();
        $typeCreate->creationDate = new DateTime();

        $nameField = $contentTypeService->newFieldDefinitionCreateStruct('name', 'ezstring');
        $nameField->names = ['eng-GB' => 'Name'];
        $nameField->position = 1;
        $nameField->isTranslatable = true;
        $nameField->isRequired = true;
        $nameField->isSearchable = true;

        $typeCreate->addFieldDefinition($nameField);

        $productField = $contentTypeService->newFieldDefinitionCreateStruct('product', Type::FIELD_TYPE_IDENTIFIER);
        $productField->names = ['eng-GB' => 'Product'];
        $productField->position = 2;
        $productField->isTranslatable = false;
        $productField->isRequired = true;
        $productField->isInfoCollector = false;

        $typeCreate->addFieldDefinition($productField);

        $contentTypeDraft = $contentTypeService->createContentType($typeCreate, [
            $contentTypeService->loadContentTypeGroupByIdentifier('Content'),
        ]);
        $contentTypeService->publishContentTypeDraft($contentTypeDraft);

        return $contentTypeService->loadContentTypeByIdentifier($identifier);
    }
}
