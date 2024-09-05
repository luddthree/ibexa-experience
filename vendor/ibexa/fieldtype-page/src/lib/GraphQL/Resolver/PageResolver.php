<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\GraphQL\Resolver;

use ArrayObject;
use GraphQL\Type\Definition\Type;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Attribute;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page;
use Overblog\GraphQLBundle\Resolver\TypeResolver;

class PageResolver
{
    /** @var \Overblog\GraphQLBundle\Resolver\TypeResolver */
    private $typeResolver;

    /** @var array */
    private $blocksTypesMap;

    /**
     * @param \Overblog\GraphQLBundle\Resolver\TypeResolver $typeResolver
     * @param array $blocksTypesMap
     */
    public function __construct(
        TypeResolver $typeResolver,
        array $blocksTypesMap = []
    ) {
        $this->typeResolver = $typeResolver;
        $this->blocksTypesMap = $blocksTypesMap;
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue $blockValue
     * @param \ArrayObject $context
     *
     * @return \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Attribute[]
     */
    public function resolvePageBlockAttributes(BlockValue $blockValue, ArrayObject $context): array
    {
        $context['BlockValue'] = $blockValue;

        return $blockValue->getAttributes();
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Attribute $value
     * @param \ArrayObject $context
     *
     * @return \GraphQL\Type\Definition\Type
     *
     * @todo provide extensibility and dynamic block attribute types for better DX
     */
    public function resolvePageBlockAttributeType(Attribute $value, ArrayObject $context): Type
    {
        return $this->typeResolver->resolve('BasePageBlockAttribute');
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue $blockValue
     *
     * @return \GraphQL\Type\Definition\Type
     */
    public function resolvePageBlockType(BlockValue $blockValue): Type
    {
        $blockType = $blockValue->getType();

        return $this->typeResolver->resolve($this->blocksTypesMap[$blockType] ?? 'BasePageBlock');
    }

    public function resolvePage(?Field $field, ArrayObject $context): ?Page
    {
        if ($field === null) {
            return null;
        }

        $page = $field->getValue()->getPage();

        $context['Page'] = $page;

        return $page;
    }
}

class_alias(PageResolver::class, 'EzSystems\EzPlatformPageFieldType\GraphQL\Resolver\PageResolver');
