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

class BlockViewsType extends BaseWorker implements Worker
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
        $schema->addType(new Builder\Input\Type($this->viewsType($args), 'enum'));
    }

    /**
     * {@inheritdoc}
     */
    public function canWork(Builder $schema, array $args): bool
    {
        if (!isset($args['BlockDefinition'])
            || !$args['BlockDefinition'] instanceof BlockDefinition
            || $schema->hasType($this->viewsType($args))) {
            return false;
        }

        $hasAtLeastOneValidName = false;
        foreach ($args['BlockDefinition']->getViews() as $viewIdentifier => $view) {
            if ($this->nameValidator->isValidName((string) $viewIdentifier)) {
                $hasAtLeastOneValidName = true;
                break;
            }
        }

        return $hasAtLeastOneValidName;
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

class_alias(BlockViewsType::class, 'EzSystems\EzPlatformPageFieldType\GraphQL\Schema\Worker\BlockViewsType');
