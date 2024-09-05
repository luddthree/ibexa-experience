<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FieldTypePage\GraphQL\Schema;

use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockAttributeDefinition;
use Ibexa\GraphQL\Schema\Builder\Input\Field;

interface BlockAttributeBuilder
{
    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockAttributeDefinition $blockAttributeDefinition
     *
     * @return \Ibexa\GraphQL\Schema\Builder\Input\Field
     */
    public function buildInput(BlockAttributeDefinition $blockAttributeDefinition): Field;
}

class_alias(BlockAttributeBuilder::class, 'EzSystems\EzPlatformPageFieldType\GraphQL\Schema\BlockAttributeBuilder');
