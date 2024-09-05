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

class RichText implements BlockAttributeBuilder
{
    /** @var \Ibexa\FieldTypePage\GraphQL\Schema\Worker\NameHelper */
    private $nameHelper;

    /**
     * @param \Ibexa\FieldTypePage\GraphQL\Schema\Worker\NameHelper $nameHelper
     */
    public function __construct(NameHelper $nameHelper)
    {
        $this->nameHelper = $nameHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function buildInput(BlockAttributeDefinition $blockAttributeDefinition): Field
    {
        $resolve = sprintf(
            '@=value.getAttribute("%s").getValue()',
            $blockAttributeDefinition->getIdentifier()
        );

        return new Field(
            $this->nameHelper->attributeField($blockAttributeDefinition),
            'RichTextBlockAttributeValue',
            ['resolve' => $resolve]
        );
    }
}

class_alias(RichText::class, 'EzSystems\EzPlatformPageFieldType\GraphQL\Schema\BlockAttributeBuilder\RichText');
