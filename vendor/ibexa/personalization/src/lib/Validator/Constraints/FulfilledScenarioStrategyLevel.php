<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
final class FulfilledScenarioStrategyLevel extends Constraint
{
    public $message = 'Cannot to set strategy "{{ current_strategy_name }}". Above strategy level "{{ prev_strategy_name }}" is not fulfilled';
}

class_alias(FulfilledScenarioStrategyLevel::class, 'Ibexa\Platform\Personalization\Validator\Constraints\FulfilledScenarioStrategyLevel');
