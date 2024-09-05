<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Form\Type\Validation\Constraint;

use Ibexa\Taxonomy\Form\Type\Validation\NestedValidator;
use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final class Nested extends Constraint
{
    /** @var callable(mixed): mixed */
    public $accessor;

    /** @var \Symfony\Component\Validator\Constraint[] */
    public array $constraints;

    /**
     * @param callable(mixed): mixed $accessor
     * @param \Symfony\Component\Validator\Constraint[] $constraints
     */
    public function __construct($accessor, array $constraints, $options = null, array $groups = null, $payload = null)
    {
        parent::__construct($options, $groups, $payload);

        $this->accessor = $accessor;
        $this->constraints = $constraints;
    }

    public function validatedBy(): string
    {
        return NestedValidator::class;
    }
}
