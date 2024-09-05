<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\GraphQL\Schema\Worker;

use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition;
use Ibexa\GraphQL\Schema\Builder;
use Ibexa\GraphQL\Schema\Initializer;
use Ibexa\GraphQL\Schema\Worker;

class BlocksIndex extends BaseWorker implements Worker, Initializer
{
    private const ENUM_TYPE = 'PageBlocksList';

    /**
     * {@inheritdoc}
     */
    public function work(Builder $schema, array $args): void
    {
        $schema->addValueToEnum(
            self::ENUM_TYPE,
            new Builder\Input\EnumValue(
                $args['BlockDefinition']->getIdentifier(),
                ['value' => $this->blockType($args)]
            )
        );
    }

    /**
     * @param \Ibexa\GraphQL\Schema\Builder $schema
     * @param array $args
     *
     * @return bool
     */
    public function canWork(Builder $schema, array $args): bool
    {
        return isset($args['BlockDefinition'])
            && $args['BlockDefinition'] instanceof BlockDefinition
            && !isset($args['BlockAttributeDefinition']);
    }

    /**
     * {@inheritdoc}
     */
    public function init(Builder $schema): void
    {
        $schema->addType(new Builder\Input\Type(self::ENUM_TYPE, 'enum'));
    }

    /**
     * @param $args
     *
     * @return string
     */
    private function blockType($args): string
    {
        return $this->getNameHelper()->blockType($args['BlockDefinition']);
    }
}

class_alias(BlocksIndex::class, 'EzSystems\EzPlatformPageFieldType\GraphQL\Schema\Worker\BlocksIndex');
