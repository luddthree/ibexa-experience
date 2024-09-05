<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\GraphQL\Schema\Worker;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockAttributeDefinition;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition;
use Ibexa\FieldTypePage\GraphQL\Schema\BlockAttributeBuilderRegistry;
use Ibexa\GraphQL\Schema\Builder;
use Ibexa\GraphQL\Schema\Worker;

class BlockAttributeField extends BaseWorker implements Worker
{
    /** @var \Ibexa\FieldTypePage\GraphQL\Schema\BlockAttributeBuilderRegistry */
    private $attributeBuilderRegistry;

    /**
     * @param \Ibexa\FieldTypePage\GraphQL\Schema\BlockAttributeBuilderRegistry $attributesBuilderRegistry
     */
    public function __construct(BlockAttributeBuilderRegistry $attributesBuilderRegistry)
    {
        $this->attributeBuilderRegistry = $attributesBuilderRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function work(Builder $schema, array $args): void
    {
        try {
            $blockAttributeDefinition = $args['BlockAttributeDefinition'];
            $attributeBuilder = $this->attributeBuilderRegistry->getAttributeBuilder($blockAttributeDefinition->getType());
            $fieldInput = $attributeBuilder->buildInput($blockAttributeDefinition);
        } catch (NotFoundException $e) {
            $fieldInput = new Builder\Input\Field(
                $this->getNameHelper()->attributeField($blockAttributeDefinition),
                'String'
            );
            $fieldInput->resolve = sprintf(
                '@=value.getAttribute("%s").getValue()',
                $blockAttributeDefinition->getIdentifier()
            );
        }

        $schema->addFieldToType($this->blockName($args), $fieldInput);
    }

    /**
     * @param $args
     *
     * @return string
     */
    private function blockName($args): string
    {
        return $this->getNameHelper()->blockType($args['BlockDefinition']);
    }

    /**
     * {@inheritdoc}
     */
    public function canWork(Builder $schema, array $args): bool
    {
        $hasBlockAttributeDefinition = $this->hasBlockAttributeDefinition($args);
        $hasAttributeBuilder = $hasBlockAttributeDefinition
                               && $this->attributeBuilderRegistry->hasAttributeBuilder(
                                   $args['BlockAttributeDefinition']->getType()
                               );

        return $this->hasBlockDefinition($args)
               && $hasBlockAttributeDefinition
               && $schema->hasType($this->blockName($args))
               && !$schema->hasTypeWithField($this->blockName($args), $this->attributeField($args))
               && $hasAttributeBuilder;
    }

    /**
     * @param array $args
     *
     * @return string
     */
    private function attributeField(array $args): string
    {
        return $this->getNameHelper()->attributeField($args['BlockAttributeDefinition']);
    }

    /**
     * @param array $args
     *
     * @return bool
     */
    private function hasBlockDefinition(array $args): bool
    {
        return isset($args['BlockDefinition']) && $args['BlockDefinition'] instanceof BlockDefinition;
    }

    /**
     * @param array $args
     *
     * @return bool
     */
    private function hasBlockAttributeDefinition(array $args): bool
    {
        return isset($args['BlockAttributeDefinition']) && $args['BlockAttributeDefinition'] instanceof BlockAttributeDefinition;
    }
}

class_alias(BlockAttributeField::class, 'EzSystems\EzPlatformPageFieldType\GraphQL\Schema\Worker\BlockAttributeField');
