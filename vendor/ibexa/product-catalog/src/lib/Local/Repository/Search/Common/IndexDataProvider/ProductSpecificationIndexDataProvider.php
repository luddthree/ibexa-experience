<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Search\Common\IndexDataProvider;

use Ibexa\Contracts\Core\Persistence\Content as SPIContent;
use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Search;
use Ibexa\ProductCatalog\FieldType\ProductSpecification\Type;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductAvailability\HandlerInterface as AvailabilityHandlerInterface;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductTypeSettings\HandlerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * @internal
 */
final class ProductSpecificationIndexDataProvider extends AbstractFieldTypeIndexDataProvider implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public const INDEX_PRODUCT_AVAILABILITY = 'product_availability';
    public const INDEX_PRODUCT_STOCK = 'product_stock';
    public const INDEX_PRODUCT_STOCK_IS_NULL = 'product_stock_null';
    public const INDEX_IS_INFINITE_STOCK = 'product_availability_is_infinite';
    public const INDEX_IS_VIRTUAL = 'is_virtual';

    private AvailabilityHandlerInterface $availabilityHandler;

    private HandlerInterface $productTypeSettingsHandler;

    public function __construct(
        AvailabilityHandlerInterface $handler,
        HandlerInterface $productTypeSettingsHandler,
        ?LoggerInterface $logger = null
    ) {
        $this->availabilityHandler = $handler;
        $this->productTypeSettingsHandler = $productTypeSettingsHandler;
        $this->logger = $logger ?? new NullLogger();
    }

    protected function doGetSearchData(SPIContent $content, Field $field): array
    {
        $productCode = $field->value->externalData['code'];

        $fields = [
            new Search\Field(
                'is_product',
                true,
                new Search\FieldType\BooleanField()
            ),
            new Search\Field(
                'product_code',
                $productCode,
                new Search\FieldType\IdentifierField(['raw' => true])
            ),
        ];

        $isAvailable = false;
        $isInfinite = false;
        $stock = null;
        if ($this->availabilityHandler->exists($productCode)) {
            $availability = $this->availabilityHandler->find($productCode);
            $isAvailable = $availability->isAvailable();
            $isInfinite = $availability->isInfinite();
            $stock = $availability->getStock();
        }

        $fields[] = new Search\Field(
            self::INDEX_PRODUCT_AVAILABILITY,
            $isAvailable,
            new Search\FieldType\BooleanField()
        );

        $fields[] = new Search\Field(
            self::INDEX_IS_INFINITE_STOCK,
            $isInfinite,
            new Search\FieldType\BooleanField()
        );

        if ($stock !== null) {
            $fields[] = new Search\Field(
                self::INDEX_PRODUCT_STOCK,
                $stock,
                new Search\FieldType\IntegerField(),
            );
        }

        $fields[] = new Search\Field(
            self::INDEX_PRODUCT_STOCK_IS_NULL,
            $stock === null,
            new Search\FieldType\BooleanField(),
        );

        $isVirtualSearchField = $this->getIsVirtualSearchField($field->fieldDefinitionId);
        if (null !== $isVirtualSearchField) {
            $fields[] = $isVirtualSearchField;
        }

        return $fields;
    }

    private function getIsVirtualSearchField(int $fieldDefinitionId): ?Search\Field
    {
        try {
            $setting = $this->productTypeSettingsHandler->findByFieldDefinitionId(
                $fieldDefinitionId,
                SPIContent\Type::STATUS_DEFINED
            );
        } catch (NotFoundException $exception) {
            $this->logger->warning(
                'Couldn’t index “is_virtual” field because of missing product type setting for field definition: ' . $fieldDefinitionId
                . PHP_EOL . 'Run upgrade script to fill missing setting. For more information, see Upgrade from v4.5 to v4.6 documentation.'
            );

            return null;
        }

        return new Search\Field(
            self::INDEX_IS_VIRTUAL,
            $setting->isVirtual(),
            new Search\FieldType\BooleanField(),
        );
    }

    protected function getFieldTypeIdentifier(): string
    {
        return Type::FIELD_TYPE_IDENTIFIER;
    }
}
