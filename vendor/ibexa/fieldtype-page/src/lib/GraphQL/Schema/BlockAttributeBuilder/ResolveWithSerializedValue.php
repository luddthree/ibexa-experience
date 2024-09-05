<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\GraphQL\Schema\BlockAttributeBuilder;

use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockAttributeDefinition;
use Ibexa\FieldTypePage\GraphQL\Schema\BlockAttributeBuilder;
use Ibexa\FieldTypePage\GraphQL\Schema\Worker\NameHelper;
use Ibexa\GraphQL\Schema\Builder\Input\Field;

class ResolveWithSerializedValue implements BlockAttributeBuilder
{
    /** @var string */
    private $type;

    /** @var \Ibexa\FieldTypePage\GraphQL\Schema\Worker\NameHelper */
    private $nameHelper;

    /**
     * @param string $type
     * @param \Ibexa\FieldTypePage\GraphQL\Schema\Worker\NameHelper $nameHelper
     */
    public function __construct(string $type, NameHelper $nameHelper)
    {
        $this->type = $type;
        $this->nameHelper = $nameHelper;
    }

    public function buildInput(BlockAttributeDefinition $blockAttributeDefinition): Field
    {
        $resolve = sprintf(
            '@=resolver("SerializedAttributeValue", [value, "%s"])',
            $blockAttributeDefinition->getIdentifier()
        );

        return new Field(
            $this->nameHelper->attributeField($blockAttributeDefinition),
            $this->type,
            ['resolve' => $resolve]
        );
    }
}

class_alias(ResolveWithSerializedValue::class, 'EzSystems\EzPlatformPageFieldType\GraphQL\Schema\BlockAttributeBuilder\ResolveWithSerializedValue');
