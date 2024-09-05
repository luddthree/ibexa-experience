<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\GraphQL\Schema\Worker;

use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockAttributeDefinition;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;

class NameHelper
{
    /** @var \Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter */
    private $caseConverter;

    public function __construct()
    {
        $this->caseConverter = new CamelCaseToSnakeCaseNameConverter(null, false);
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition $blockDefinition
     *
     * @return string
     */
    public function blockType(BlockDefinition $blockDefinition): string
    {
        return ucfirst($this->toCamelCase($blockDefinition->getIdentifier())) . 'PageBlock';
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition $blockDefinition
     *
     * @return string
     */
    public function viewsType(BlockDefinition $blockDefinition): string
    {
        return ucfirst($this->toCamelCase($blockDefinition->getIdentifier())) . 'PageBlockViews';
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockAttributeDefinition $blockAttributeDefinition
     *
     * @return string
     */
    public function attributeField(BlockAttributeDefinition $blockAttributeDefinition): string
    {
        return lcfirst($this->toCamelCase($blockAttributeDefinition->getIdentifier()));
    }

    /**
     * @param string $string
     *
     * @return string
     */
    private function toCamelCase(string $string): string
    {
        return $this->caseConverter->denormalize($string);
    }
}

class_alias(NameHelper::class, 'EzSystems\EzPlatformPageFieldType\GraphQL\Schema\Worker\NameHelper');
