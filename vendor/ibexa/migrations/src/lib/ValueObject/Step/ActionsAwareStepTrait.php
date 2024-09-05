<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Step;

trait ActionsAwareStepTrait
{
    /** @var \Ibexa\Migration\ValueObject\Step\Action[] */
    private $actions = [];

    public function addAction(Action $action): void
    {
        $this->actions[] = $action;
    }

    /**
     * @return \Ibexa\Migration\ValueObject\Step\Action[]
     */
    public function getActions(): array
    {
        return $this->actions;
    }
}

class_alias(ActionsAwareStepTrait::class, 'Ibexa\Platform\Migration\ValueObject\Step\ActionsAwareStepTrait');
