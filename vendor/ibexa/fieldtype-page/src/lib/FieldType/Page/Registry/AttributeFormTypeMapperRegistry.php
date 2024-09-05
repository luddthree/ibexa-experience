<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\FieldType\Page\Registry;

use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Attribute\FormTypeMapper\AttributeFormTypeMapperInterface;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockAttributeDefinition;
use Ibexa\FieldTypePage\Exception\Registry\AttributeFormMapperNotFoundException;

class AttributeFormTypeMapperRegistry
{
    /** @var \Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Attribute\FormTypeMapper\AttributeFormTypeMapperInterface[] */
    private $mappers;

    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Attribute\FormTypeMapper\AttributeFormTypeMapperInterface[] $mappers
     */
    public function __construct(array $mappers = [])
    {
        $this->mappers = $mappers;
    }

    /**
     * @return \Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Attribute\FormTypeMapper\AttributeFormTypeMapperInterface[]
     */
    public function getMappers(): array
    {
        return $this->mappers;
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Attribute\FormTypeMapper\AttributeFormTypeMapperInterface[] $mappers
     */
    public function setMappers(array $mappers): void
    {
        $this->mappers = $mappers;
    }

    /**
     * @param string $type
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Attribute\FormTypeMapper\AttributeFormTypeMapperInterface $mapper
     */
    public function addMapper(string $type, AttributeFormTypeMapperInterface $mapper): void
    {
        $this->mappers[$type] = $mapper;
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockAttributeDefinition $blockAttributeDefinition
     *
     * @return \Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Attribute\FormTypeMapper\AttributeFormTypeMapperInterface
     *
     * @throws \Ibexa\FieldTypePage\Exception\Registry\AttributeFormMapperNotFoundException
     */
    public function getMapperForAttribute(
        BlockAttributeDefinition $blockAttributeDefinition
    ): AttributeFormTypeMapperInterface {
        return $this->getMapper($blockAttributeDefinition->getType());
    }

    /**
     * @param string $type
     *
     * @return \Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Attribute\FormTypeMapper\AttributeFormTypeMapperInterface
     *
     * @throws \Ibexa\FieldTypePage\Exception\Registry\AttributeFormMapperNotFoundException
     */
    public function getMapper(string $type): AttributeFormTypeMapperInterface
    {
        if ($this->hasMapper($type)) {
            return $this->mappers[$type];
        }

        throw new AttributeFormMapperNotFoundException($type);
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    public function hasMapper(string $type): bool
    {
        return isset($this->mappers[$type]);
    }
}

class_alias(AttributeFormTypeMapperRegistry::class, 'EzSystems\EzPlatformPageFieldType\FieldType\Page\Registry\AttributeFormTypeMapperRegistry');
