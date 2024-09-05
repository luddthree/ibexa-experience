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
final class UniqueScenarioName extends Constraint
{
    /** @var string */
    public $message = 'Scenario with name "{{ name }}" already exist';

    /** @var int */
    public $customerId;

    /** @var string */
    public $actionType;

    /** @var string|null */
    public $referenceCode;
}

class_alias(UniqueScenarioName::class, 'Ibexa\Platform\Personalization\Validator\Constraints\UniqueScenarioName');
