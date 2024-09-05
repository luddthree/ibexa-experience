<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\GraphQL\Schema\Worker;

use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition;
use Ibexa\GraphQL\Schema\Builder;
use Ibexa\GraphQL\Schema\Domain\NameValidator;
use Ibexa\GraphQL\Schema\Worker;

class BlockViewsValue extends BaseWorker implements Worker
{
    /** @var \Ibexa\GraphQL\Schema\Domain\NameValidator */
    private $nameValidator;

    public function __construct(NameValidator $nameValidator)
    {
        $this->nameValidator = $nameValidator;
    }

    /**
     * {@inheritdoc}
     */
    public function work(Builder $schema, array $args): void
    {
        $schema->addValueToEnum(
            $this->viewsType($args),
            new Builder\Input\EnumValue(
                $args['BlockView']['identifier'],
                [
                    'value' => $args['BlockView']['template'],
                    'description' => $args['BlockView']['name'],
                ]
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function canWork(Builder $schema, array $args): bool
    {
        if (
            !isset($args['BlockDefinition'])
            || !$args['BlockDefinition'] instanceof BlockDefinition
            || !isset($args['BlockView'])) {
            return false;
        }

        $identifier = (string) $args['BlockView']['identifier'];
        if (!$this->nameValidator->isValidName($identifier)) {
            $this->nameValidator->generateInvalidNameWarning((string) $args['BlockView']['name'], $identifier);

            return false;
        }

        return true;
    }

    /**
     * @param $args
     *
     * @return string
     */
    private function viewsType($args): string
    {
        return $this->getNameHelper()->viewsType($args['BlockDefinition']);
    }
}

class_alias(BlockViewsValue::class, 'EzSystems\EzPlatformPageFieldType\GraphQL\Schema\Worker\BlockViewsValue');
