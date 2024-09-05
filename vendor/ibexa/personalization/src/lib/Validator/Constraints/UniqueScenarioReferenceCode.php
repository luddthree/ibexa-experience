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
final class UniqueScenarioReferenceCode extends Constraint
{
    /** @var string */
    public $message = 'Scenario with the reference code "{{ string }}" already exist';

    /** @var int */
    public $customerId;
}

class_alias(UniqueScenarioReferenceCode::class, 'Ibexa\Platform\Personalization\Validator\Constraints\UniqueScenarioReferenceCode');
