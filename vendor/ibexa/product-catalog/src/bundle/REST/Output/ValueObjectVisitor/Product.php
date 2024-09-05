<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Output\ValueObjectVisitor;

use Ibexa\Bundle\ProductCatalog\REST\Value\Attribute;
use Ibexa\Bundle\ProductCatalog\REST\Value\Availability;
use Ibexa\Bundle\ProductCatalog\REST\Value\Price;
use Ibexa\Bundle\ProductCatalog\REST\Value\ProductType;
use Ibexa\Bundle\ProductCatalog\REST\Value\Thumbnail;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\ProductCatalog\Values\AvailabilityAwareInterface;
use Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\PriceAwareInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Contracts\Rest\Output\Generator;
use Ibexa\Contracts\Rest\Output\ValueObjectVisitor;
use Ibexa\Contracts\Rest\Output\Visitor;
use Ibexa\Rest\Server\Values\RestContent;

final class Product extends ValueObjectVisitor
{
    private const OBJECT_IDENTIFIER = 'Product';
    private const ATTRIBUTE_IDENTIFIER_IS_VARIANT = 'isVariant';
    private const ATTRIBUTE_IDENTIFIER_IS_BASE_PRODUCT = 'isBaseProduct';
    private const ATTRIBUTE_IDENTIFIER_CODE = 'code';
    private const ATTRIBUTE_IDENTIFIER_NAME = 'name';
    private const ATTRIBUTE_IDENTIFIER_CREATED_AT = 'created_at';
    private const ATTRIBUTE_IDENTIFIER_UPDATED_AT = 'updated_at';
    private const ATTRIBUTES_LIST_OBJECT_IDENTIFIER = 'AttributesList';

    protected ContentService $contentService;

    public function __construct(ContentService $contentService)
    {
        $this->contentService = $contentService;
    }

    /**
     * @param \Ibexa\Bundle\ProductCatalog\REST\Value\Product $data
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $product = $data->product;
        $generator->startObjectElement(self::OBJECT_IDENTIFIER);
        $visitor->setHeader('Content-Type', $generator->getMediaType(self::OBJECT_IDENTIFIER));

        $generator->valueElement(self::ATTRIBUTE_IDENTIFIER_CODE, $product->getCode());
        $generator->valueElement(self::ATTRIBUTE_IDENTIFIER_IS_BASE_PRODUCT, $product->isBaseProduct());
        $generator->valueElement(self::ATTRIBUTE_IDENTIFIER_IS_VARIANT, $product->isVariant());
        $generator->valueElement(self::ATTRIBUTE_IDENTIFIER_NAME, $product->getName());

        $restProductType = new ProductType($product->getProductType());
        $visitor->visitValueObject($restProductType);

        if ($product instanceof ContentAwareProductInterface) {
            $restContent = $this->populateRestContent($product->getContent());
            $visitor->visitValueObject($restContent);
        }

        $restThumbnail = new Thumbnail($product->getThumbnail());
        $visitor->visitValueObject($restThumbnail);

        $generator->valueElement(
            self::ATTRIBUTE_IDENTIFIER_CREATED_AT,
            $product->getCreatedAt()->getTimestamp()
        );

        $generator->valueElement(
            self::ATTRIBUTE_IDENTIFIER_UPDATED_AT,
            $product->getUpdatedAt()->getTimestamp()
        );

        $generator->startList(self::ATTRIBUTES_LIST_OBJECT_IDENTIFIER);

        foreach ($product->getAttributes() as $attribute) {
            $restAttribute = new Attribute($attribute->getAttributeDefinition(), $attribute->getValue());
            $visitor->visitValueObject($restAttribute);
        }

        $generator->endList(self::ATTRIBUTES_LIST_OBJECT_IDENTIFIER);

        $this->visitAvailability($visitor, $product);
        $this->visitPrice($visitor, $product);

        $generator->endObjectElement(self::OBJECT_IDENTIFIER);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    private function populateRestContent(Content $content): RestContent
    {
        $relationsArray = [];
        $contentVersion = $this->contentService->loadContent($content->id, Language::ALL);
        $relations = $this->contentService->loadRelations($contentVersion->getVersionInfo());

        foreach ($relations as $relation) {
            $relationsArray[] = $relation;
        }

        return new RestContent(
            $content->contentInfo,
            null,
            $contentVersion,
            $content->getContentType(),
            $relationsArray
        );
    }

    private function visitAvailability(Visitor $visitor, ProductInterface $product): void
    {
        if (
            !$product->isBaseProduct() &&
            $product instanceof AvailabilityAwareInterface &&
            $product->hasAvailability()
        ) {
            $restAvailability = new Availability($product->getAvailability());
            $visitor->visitValueObject($restAvailability);
        }
    }

    private function visitPrice(Visitor $visitor, ProductInterface $product): void
    {
        if (
            $product instanceof PriceAwareInterface &&
            $product->getPrice() !== null
        ) {
            $restPrice = new Price($product->getPrice());
            $visitor->visitValueObject($restPrice);
        }
    }
}
