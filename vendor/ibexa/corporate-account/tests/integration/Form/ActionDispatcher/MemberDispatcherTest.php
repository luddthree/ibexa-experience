<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\CorporateAccount\Form\ActionDispatcher;

use Ibexa\Contracts\ContentForms\Data\Content\FieldData;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\RoleService;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\RoleLimitation;
use Ibexa\Contracts\Core\Repository\Values\User\Role;
use Ibexa\Contracts\Core\Repository\Values\User\UserGroup;
use Ibexa\Contracts\CorporateAccount\Service\CompanyService;
use Ibexa\Contracts\CorporateAccount\Service\MemberService;
use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\Contracts\CorporateAccount\Values\Member;
use Ibexa\Contracts\CorporateAccount\Values\MemberCreateStruct;
use Ibexa\Core\FieldType\User\Value;
use Ibexa\CorporateAccount\Form\ActionDispatcher\MemberDispatcher;
use Ibexa\CorporateAccount\Form\Data\Member\MemberCreateData;
use Ibexa\CorporateAccount\Form\Data\Member\MemberRoleChangeData;
use Ibexa\CorporateAccount\Form\MemberFormFactory;
use Ibexa\Tests\Integration\CorporateAccount\IbexaKernelTestCase;

final class MemberDispatcherTest extends IbexaKernelTestCase
{
    private CompanyService $companyService;

    private MemberFormFactory $formFactory;

    private MemberDispatcher $actionDispatcher;

    private ContentTypeService $contentTypeService;

    private Company $company;

    private UserGroup $userGroup;

    private RoleService $roleService;

    private MemberService $memberService;

    protected function setUp(): void
    {
        self::setAdministratorUser();

        $this->formFactory = self::getMemberFormFactory();
        $this->actionDispatcher = self::getMemberDispatcher();
        $this->companyService = self::getCompanyService();
        $this->contentTypeService = self::getContentTypeService();
        $this->roleService = self::getRoleService();
        $this->memberService = self::getMemberService();

        $this->company = $this->companyService->createCompany(
            $this->getTestCompanyCreateStruct()
        );

        $userGroupContent = $this->companyService->createCompanyMembersUserGroup($this->company);

        /* Refresh after creating member group */
        $this->company = $this->companyService->getCompany($this->company->getId());
        $this->userGroup = self::getUserService()->loadUserGroup($userGroupContent->id);
    }

    public function testCreateMember(): void
    {
        $createForm = $this->formFactory->getCreateForm(
            $this->userGroup,
            'eng-GB'
        );

        $memberContentType = $this->contentTypeService->loadContentTypeByIdentifier('member');
        $memberRole = $this->roleService->loadRoleByIdentifier('Company admin');

        $data = new MemberCreateData();
        $data->mainLanguageCode = 'eng-GB';
        $data->contentType = $memberContentType;
        $data->email = 'test.company@foo.ltd';
        $data->login = 'test_admin';
        $data->password = 'Qwerty123456';
        $data->enabled = true;
        $data->setRole($memberRole);

        $userValue = new Value();
        $userValue->email = $data->email;
        $userValue->enabled = $data->enabled;
        $userValue->login = $data->login;
        $userValue->plainPassword = $data->password;

        $data->addFieldData(new FieldData([
            'fieldDefinition' => $memberContentType->getFieldDefinition('user'),
            'field' => new Field([
                'fieldDefIdentifier' => 'user',
                'languageCode' => 'eng-GB',
            ]),
            'value' => $userValue,
        ]));

        $data->addFieldData(new FieldData([
            'fieldDefinition' => $memberContentType->getFieldDefinition('first_name'),
            'field' => new Field([
                'fieldDefIdentifier' => 'first_name',
                'languageCode' => 'eng-GB',
            ]),
            'value' => 'John',
        ]));

        $data->addFieldData(new FieldData([
            'fieldDefinition' => $memberContentType->getFieldDefinition('last_name'),
            'field' => new Field([
                'fieldDefIdentifier' => 'last_name',
                'languageCode' => 'eng-GB',
            ]),
            'value' => 'Doe',
        ]));

        $data->addFieldData(new FieldData([
            'fieldDefinition' => $memberContentType->getFieldDefinition('phone_number'),
            'field' => new Field([
                'fieldDefIdentifier' => 'phone_number',
                'languageCode' => 'eng-GB',
            ]),
            'value' => '123 456 789',
        ]));

        $this->actionDispatcher->dispatchFormAction(
            $createForm,
            $data,
            'create',
            ['company' => $this->company]
        );

        $members = $this->memberService->getCompanyMembers($this->company);

        self::assertCount(1, $members);

        /** @var \Ibexa\Contracts\CorporateAccount\Values\Member $member */
        $member = reset($members);

        self::assertEquals('Company admin', $member->getRole()->identifier);
        self::assertEquals('John Doe', $member->getName());

        $this->assertPermissionRole($memberRole, $member);
    }

    public function changeMemberRole(): void
    {
        $memberCreateStruct = new MemberCreateStruct();

        $memberCreateStruct->login = 'role_change';
        $memberCreateStruct->password = 'Qwerty123456';
        $memberCreateStruct->email = 'member@foo.ltd';

        $userValue = new Value();
        $userValue->email = $memberCreateStruct->email;
        $userValue->login = $memberCreateStruct->login;
        $userValue->plainPassword = $memberCreateStruct->password;

        $memberCreateStruct->setField(
            'user',
            $userValue
        );
        $memberCreateStruct->setField(
            'first_name',
            'role'
        );
        $memberCreateStruct->setField(
            'last_name',
            'change'
        );
        $memberCreateStruct->setField(
            'phone_number',
            '13 456 789'
        );

        $oldRole = $this->roleService->loadRoleByIdentifier('Company admin');

        $member = $this->memberService->createMember(
            $this->company,
            $memberCreateStruct,
            $oldRole
        );

        self::assertEquals('Company admin', $member->getRole()->identifier);

        $newRole = $this->roleService->loadRoleByIdentifier('Company buyer');

        $changeRoleForm = $this->formFactory->getChangeRoleForm();
        $memberRoleChangeData = new MemberRoleChangeData();
        $memberRoleChangeData->setMember($member);
        $memberRoleChangeData->setNewRole($newRole);

        $this->actionDispatcher->dispatchFormAction(
            $changeRoleForm,
            $memberRoleChangeData,
            'create',
            ['company' => $this->company]
        );

        $refreshedMember = $this->memberService->getMember($member->getId(), $member->getCompany());

        self::assertEquals('Company buyer', $refreshedMember->getRole()->identifier);

        $this->assertPermissionRole($newRole, $member);
    }

    private function assertPermissionRole(
        Role $newRole,
        Member $member
    ): void {
        $roleAssignments = $this->roleService->getRoleAssignments($newRole);
        self::assertCount(1, $roleAssignments);

        /** @var \Ibexa\Contracts\Core\Repository\Values\User\UserRoleAssignment $roleAssignment */
        $roleAssignment = reset($roleAssignments);
        self::assertEquals($roleAssignment->getRole()->identifier, $member->getRole()->identifier);
        self::assertEquals($roleAssignment->getUser()->id, $member->getId());
        self::assertInstanceOf(RoleLimitation::class, $roleAssignment->getRoleLimitation());
        self::assertEquals($roleAssignment->getRoleLimitation()->limitationValues, [$this->company->getLocationPath()]);
    }
}
