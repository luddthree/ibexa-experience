<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Validation\Builder;

use Ibexa\Contracts\Core\Repository\Values\User\Role;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Validator\ContextualValidatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @internal
 */
final class MemberRoleValidatorBuilder
{
    private ContextualValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator->startContext();
    }

    /**
     * @param array<string, string> $allowedRolesIdentifiers
     */
    public function validateRole(Role $role, array $allowedRolesIdentifiers): self
    {
        $this
            ->validator
            ->atPath('identifier')
            ->validate(
                $role->identifier,
                new Choice(
                    [
                        'choices' => array_values($allowedRolesIdentifiers),
                        'message' => '{{ value }} is not a valid Corporate Account Role. Use one of the following instead: {{ choices }}',
                    ],
                )
            );

        return $this;
    }

    public function build(): ContextualValidatorInterface
    {
        return $this->validator;
    }
}
