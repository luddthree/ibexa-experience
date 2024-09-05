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
use Ibexa\GraphQL\Schema\Builder\Input;

/**
 * A Block Attribute Builder that is configured through its constructor.
 * It requires a GraphQL type, and accepts an optional resolve string.
 */
class Configurable implements BlockAttributeBuilder
{
    /** @var string */
    private $type;

    /** @var string|null */
    private $resolve;

    /** @var \Ibexa\FieldTypePage\GraphQL\Schema\Worker\NameHelper */
    private $nameHelper;

    /**
     * @param \Ibexa\FieldTypePage\GraphQL\Schema\Worker\NameHelper $nameHelper
     * @param string $type
     * @param string|null $resolve
     */
    public function __construct(NameHelper $nameHelper, string $type, ?string $resolve = null)
    {
        $this->type = $type;
        $this->resolve = $resolve;
        $this->nameHelper = $nameHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function buildInput(BlockAttributeDefinition $blockAttributeDefinition): Input\Field
    {
        $input = new Input\Field(
            $this->nameHelper->attributeField($blockAttributeDefinition),
            $this->type
        );

        if (isset($this->resolve)) {
            $input->resolve = $this->resolve;
        }

        return $input;
    }
}

class_alias(Configurable::class, 'EzSystems\EzPlatformPageFieldType\GraphQL\Schema\BlockAttributeBuilder\Configurable');
