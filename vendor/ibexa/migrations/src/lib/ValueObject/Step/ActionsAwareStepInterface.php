<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Step;

interface ActionsAwareStepInterface extends StepInterface
{
    public function addAction(Action $action): void;

    /**
     * @return \Ibexa\Migration\ValueObject\Step\Action[]
     */
    public function getActions(): array;
}

class_alias(ActionsAwareStepInterface::class, 'Ibexa\Platform\Migration\ValueObject\Step\ActionsAwareStepInterface');
