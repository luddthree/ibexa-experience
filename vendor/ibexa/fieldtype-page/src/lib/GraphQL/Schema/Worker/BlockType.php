<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\GraphQL\Schema\Worker;

use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition;
use Ibexa\GraphQL\Schema\Builder;
use Ibexa\GraphQL\Schema\Worker;

class BlockType extends BaseWorker implements Worker
{
    /**
     * {@inheritdoc}
     */
    public function work(Builder $schema, array $args): void
    {
        $schema->addType(
            new Builder\Input\Type(
                $this->blockType($args),
                'object',
                ['inherits' => 'BasePageBlock']
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function canWork(Builder $schema, array $args): bool
    {
        return
            isset($args['BlockDefinition'])
            && $args['BlockDefinition'] instanceof BlockDefinition
            && !$schema->hasType($this->blockType($args));
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

class_alias(BlockType::class, 'EzSystems\EzPlatformPageFieldType\GraphQL\Schema\Worker\BlockType');
