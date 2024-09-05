<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Form\Type\Member;

use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\RoleService;
use Ibexa\Contracts\Core\Repository\Values\User\Role;
use Ibexa\CorporateAccount\Configuration\CorporateAccountConfiguration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MemberRoleChoiceType extends AbstractType
{
    private Repository $repository;

    private CorporateAccountConfiguration $corporateAccountConfiguration;

    private RoleService $roleService;

    public function __construct(
        Repository $repository,
        CorporateAccountConfiguration $corporateAccountConfiguration,
        RoleService $roleService
    ) {
        $this->repository = $repository;
        $this->corporateAccountConfiguration = $corporateAccountConfiguration;
        $this->roleService = $roleService;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $rolesIds = $this->corporateAccountConfiguration->getCorporateAccountsRolesIdentifiers();

        $resolver->setDefaults([
            'choice_loader' => new CallbackChoiceLoader(
                fn () => array_filter($this->repository->sudo(
                    fn () => [...$this->roleService->loadRoles()]
                ), static fn (Role $role) => in_array($role->identifier, $rolesIds, true))
            ),
            'choice_name' => 'identifier',
            'choice_value' => 'id',
            'choice_label' => 'identifier',
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
