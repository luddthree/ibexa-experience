<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\GraphQL\Resolver;

use ArrayObject;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\ContentId;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Attribute;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Ibexa\FieldTypePage\FieldType\Page\Block\Attribute\ValueConverter;
use Ibexa\FieldTypePage\Serializer\AttributeSerializationDispatcher;
use Ibexa\GraphQL\DataLoader\ContentLoader;
use Ibexa\GraphQL\ItemFactory;
use Ibexa\GraphQL\Value\Item;

class BlockAttributeStorageValueResolver
{
    /** @var \Ibexa\FieldTypePage\Serializer\AttributeSerializationDispatcher */
    private $attributeSerializationDispatcher;

    /** @var \Ibexa\FieldTypePage\FieldType\Page\Block\Attribute\ValueConverter\LocationList */
    private $locationListValueConverter;

    /** @var \Ibexa\FieldTypePage\FieldType\Page\Block\Attribute\ValueConverter\ContentTypeList */
    private $contentTypeValueConverter;

    /** @var \Ibexa\FieldTypePage\FieldType\Page\Block\Attribute\ValueConverter\Multiple */
    private $multipleValueConverter;

    /** @var \Ibexa\GraphQL\DataLoader\ContentLoader */
    private $contentDataLoader;

    /** @var \Ibexa\GraphQL\ItemFactory */
    private $itemFactory;

    /**
     * @param \Ibexa\FieldTypePage\Serializer\AttributeSerializationDispatcher $attributeSerializationDispatcher
     * @param \Ibexa\FieldTypePage\FieldType\Page\Block\Attribute\ValueConverter\LocationList $locationListValueConverter
     * @param \Ibexa\FieldTypePage\FieldType\Page\Block\Attribute\ValueConverter\ContentTypeList $contentTypeValueConverter
     * @param \Ibexa\FieldTypePage\FieldType\Page\Block\Attribute\ValueConverter\Multiple $multipleValueConverter
     * @param \Ibexa\GraphQL\DataLoader\ContentLoader $contentDataLoader
     */
    public function __construct(
        AttributeSerializationDispatcher $attributeSerializationDispatcher,
        ValueConverter\LocationList $locationListValueConverter,
        ValueConverter\ContentTypeList $contentTypeValueConverter,
        ValueConverter\Multiple $multipleValueConverter,
        ContentLoader $contentDataLoader,
        ItemFactory $currentSiteItemFactory
    ) {
        $this->attributeSerializationDispatcher = $attributeSerializationDispatcher;
        $this->locationListValueConverter = $locationListValueConverter;
        $this->contentTypeValueConverter = $contentTypeValueConverter;
        $this->multipleValueConverter = $multipleValueConverter;
        $this->contentDataLoader = $contentDataLoader;
        $this->itemFactory = $currentSiteItemFactory;
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue $blockValue
     * @param string $attributeIdentifier
     *
     * @return mixed|null
     */
    public function resolveAttributeSerializedValue(BlockValue $blockValue, string $attributeIdentifier)
    {
        $value = $this->attributeSerializationDispatcher->serialize(
            $blockValue,
            $blockValue->getAttribute($attributeIdentifier)
        );

        return $value;
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Attribute $attribute
     * @param \ArrayObject $context
     *
     * @return mixed|null
     */
    public function resolveAttributeSerializedValueFromContext(Attribute $attribute, ArrayObject $context)
    {
        $blockValue = $context['BlockValue'];

        $value = $this->attributeSerializationDispatcher->serialize($blockValue, $attribute);

        return $value;
    }

    /**
     * @param string $contentId
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Content
     */
    public function resolveEmbed(int $contentId): Item
    {
        return $this->itemFactory->fromContent(
            $this->contentDataLoader->findSingle(new ContentId($contentId))
        );
    }

    /**
     * @param string $value
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Location[]
     */
    public function resolveLocationList(string $value): array
    {
        return $this->locationListValueConverter->fromStorageValue($value);
    }

    /**
     * @param string $value
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType[]
     */
    public function resolveContentTypeList(string $value): array
    {
        return $this->contentTypeValueConverter->fromStorageValue($value);
    }

    /**
     * @param string $value
     *
     * @return string[]
     */
    public function resolveMultiple(string $value): array
    {
        return $this->multipleValueConverter->fromStorageValue($value);
    }
}

class_alias(BlockAttributeStorageValueResolver::class, 'EzSystems\EzPlatformPageFieldType\GraphQL\Resolver\BlockAttributeStorageValueResolver');
