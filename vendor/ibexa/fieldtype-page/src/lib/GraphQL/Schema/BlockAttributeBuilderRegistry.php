<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\GraphQL\Schema;

use Ibexa\Core\Base\Exceptions\NotFoundException;

class BlockAttributeBuilderRegistry
{
    /** @var \Ibexa\FieldTypePage\GraphQL\Schema\BlockAttributeBuilder[] */
    private $attributeBuilders;

    /**
     * @param \Ibexa\FieldTypePage\GraphQL\Schema\BlockAttributeBuilder[] $attributeBuilders
     */
    public function __construct(array $attributeBuilders = [])
    {
        $this->attributeBuilders = $attributeBuilders;
    }

    /**
     * @return \Ibexa\FieldTypePage\GraphQL\Schema\BlockAttributeBuilder[]
     */
    public function getAttributeBuilders(): array
    {
        return $this->attributeBuilders;
    }

    /**
     * @param \Ibexa\FieldTypePage\GraphQL\Schema\BlockAttributeBuilder[] $attributeBuilders
     */
    public function setAttributeBuilders(array $attributeBuilders): void
    {
        $this->attributeBuilders = $attributeBuilders;
    }

    /**
     * @param string $type
     *
     * @return \Ibexa\FieldTypePage\GraphQL\Schema\BlockAttributeBuilder
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function getAttributeBuilder(string $type): BlockAttributeBuilder
    {
        if (!isset($this->attributeBuilders[$type])) {
            throw new NotFoundException('BlockAttributeBuilder', $type);
        }

        return $this->attributeBuilders[$type];
    }

    /**
     * @param string $type
     * @param \Ibexa\FieldTypePage\GraphQL\Schema\BlockAttributeBuilder $attributeBuilder
     */
    public function setAttributeBuilder(string $type, BlockAttributeBuilder $attributeBuilder): void
    {
        $this->attributeBuilders[$type] = $attributeBuilder;
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    public function hasAttributeBuilder(string $type): bool
    {
        return isset($this->attributeBuilders[$type]);
    }
}

class_alias(BlockAttributeBuilderRegistry::class, 'EzSystems\EzPlatformPageFieldType\GraphQL\Schema\BlockAttributeBuilderRegistry');
