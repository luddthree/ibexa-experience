<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount;

use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\RoleService;
use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\ContentTypeIdentifier;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\SubtreeLimitation;
use Ibexa\Contracts\Core\Repository\Values\User\Role;
use Ibexa\Contracts\Core\Repository\Values\User\RoleAssignment;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\CorporateAccount\Service\MemberService as MemberServiceInterface;
use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\Contracts\CorporateAccount\Values\Member;
use Ibexa\Contracts\CorporateAccount\Values\MemberAssignment;
use Ibexa\Contracts\CorporateAccount\Values\MemberCreateStruct;
use Ibexa\Contracts\CorporateAccount\Values\MemberUpdateStruct;
use Ibexa\Core\Base\Exceptions\BadStateException;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\Core\Repository\Values\Content\ContentUpdateStruct;
use Ibexa\CorporateAccount\Configuration\CorporateAccountConfiguration;
use Ibexa\CorporateAccount\Exception\ValidationFailedException;
use Ibexa\CorporateAccount\Persistence\Legacy\MemberAssignment\DomainMapper;
use Ibexa\CorporateAccount\Persistence\Legacy\MemberAssignment\Gateway\StorageSchema;
use Ibexa\CorporateAccount\Persistence\Legacy\MemberAssignment\HandlerInterface;
use Ibexa\CorporateAccount\Validation\Builder\MemberRoleValidatorBuilder;
use Ibexa\CorporateAccount\Values\Mapper\DomainMapperInterface;
use LogicException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class MemberService implements MemberServiceInterface
{
    private ConfigResolverInterface $configResolver;

    private CorporateAccountConfiguration $corporateAccountConfiguration;

    private DomainMapperInterface $domainMapper;

    private SearchService $searchService;

    private UserService $userService;

    private RoleService $roleService;

    private HandlerInterface $handler;

    private DomainMapper $memberAssignmentDomainMapper;

    private ContentTypeService $contentTypeService;

    private ValidatorInterface $validator;

    public function __construct(
        ConfigResolverInterface $configResolver,
        CorporateAccountConfiguration $corporateAccountConfiguration,
        DomainMapperInterface $domainMapper,
        SearchService $searchService,
        UserService $userService,
        RoleService $roleService,
        HandlerInterface $handler,
        DomainMapper $memberAssignmentDomainMapper,
        ContentTypeService $contentTypeService,
        ValidatorInterface $validator
    ) {
        $this->domainMapper = $domainMapper;
        $this->searchService = $searchService;
        $this->corporateAccountConfiguration = $corporateAccountConfiguration;
        $this->userService = $userService;
        $this->roleService = $roleService;
        $this->handler = $handler;
        $this->configResolver = $configResolver;
        $this->memberAssignmentDomainMapper = $memberAssignmentDomainMapper;
        $this->contentTypeService = $contentTypeService;
        $this->validator = $validator;
    }

    public function getMemberAssignment(Member $member, Company $company): MemberAssignment
    {
        return $this->memberAssignmentDomainMapper->createFromPersistence(
            $this->handler->findBy([
                StorageSchema::COLUMN_MEMBER_ID => $member->getId(),
                StorageSchema::COLUMN_COMPANY_ID => $company->getId(),
            ])[0]
        );
    }

    public function getMemberAssignmentByUser(User $user, Company $company): MemberAssignment
    {
        return $this->memberAssignmentDomainMapper->createFromPersistence(
            $this->handler->findBy([
                StorageSchema::COLUMN_MEMBER_ID => $user->id,
                StorageSchema::COLUMN_COMPANY_ID => $company->getId(),
            ])[0]
        );
    }

    /**
     * @return iterable<\Ibexa\Contracts\CorporateAccount\Values\MemberAssignment>
     */
    public function getMemberAssignments(Member $member): iterable
    {
        return $this->getMemberAssignmentsByMemberId($member->getId());
    }

    /**
     * @return iterable<\Ibexa\Contracts\CorporateAccount\Values\MemberAssignment>
     */
    public function getMemberAssignmentsByMemberId(int $memberId): iterable
    {
        return
            array_map(
                fn (Persistence\Values\MemberAssignment $memberAssignment) => $this->memberAssignmentDomainMapper->createFromPersistence($memberAssignment),
                $this->handler->findBy(['member_id' => $memberId])
            );
    }

    public function getRoleAssignmentByUser(User $user, Role $role, string $locationPath): RoleAssignment
    {
        $roleAssignments = $this->roleService->getRoleAssignmentsForUser($user);

        foreach ($roleAssignments as $roleAssignment) {
            if ($roleAssignment->getRole()->identifier !== $role->identifier) {
                continue;
            }

            $existingRoleLimitation = $roleAssignment->getRoleLimitation();
            if ($existingRoleLimitation === null) {
                continue;
            }

            $roleCompanyPath = reset($existingRoleLimitation->limitationValues);
            if ($locationPath === $roleCompanyPath) {
                return $roleAssignment;
            }
        }

        throw new NotFoundException(
            'RoleAssignment',
            sprintf(
                'UserId: %d, RoleIdentifier: %s, Subtree: %s',
                $user->getUserId(),
                $role->identifier,
                $locationPath
            )
        );
    }

    public function getRoleAssignment(Member $member): RoleAssignment
    {
        return $this->getRoleAssignmentByUser(
            $member->getUser(),
            $member->getRole(),
            $member->getCompany()->getLocationPath()
        );
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ForbiddenException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\CorporateAccount\Exception\ValidationFailedExceptionInterface
     */
    public function setMemberRole(Member $member, Role $role): void
    {
        $user = $member->getUser();
        $company = $member->getCompany();
        $companyPath = $company->getLocationPath();

        $this->assertValidCorporateAccountRole($role);

        $roleAssignments = $this->roleService->getRoleAssignmentsForUser($user);
        $companyLocation = $company->getContent()->getVersionInfo()->getContentInfo()->getMainLocation();

        if ($companyLocation === null) {
            throw new BadStateException('mainLocation', 'Creating member in company without location is not possible');
        }

        $roleAssignment = $this->getRoleAssignment($member);
        $this->roleService->removeRoleAssignment($roleAssignment);

        $roleLimitation = new SubtreeLimitation([
            'limitationValues' => [$companyPath],
        ]);

        $this->roleService->assignRoleToUser(
            $role,
            $user,
            $roleLimitation
        );
    }

    public function getMember(int $memberId, Company $company): Member
    {
        $user = $this->userService->loadUser($memberId);

        if ($user->getContentType()->identifier !== $this->corporateAccountConfiguration->getMemberContentTypeIdentifier()) {
            throw new InvalidArgumentException('$memberId', 'User is not a member');
        }

        $memberAssignment = $this->getMemberAssignmentByUser($user, $company);
        $role = $this->roleService->loadRoleByIdentifier($memberAssignment->getMemberRole());

        return $this->domainMapper->mapMember($user, $company, $role);
    }

    public function getCompanyMembers(
        Company $company,
        ?Criterion $filter = null,
        array $sortClauses = [],
        ?int $limit = 25,
        int $offset = 0
    ): array {
        $query = $this->getCompanyMembersQuery(
            $company,
            $filter,
            $sortClauses,
            $limit,
            $offset
        );

        $result = $this->searchService->findContent($query);

        return array_map(function (SearchHit $searchHit) use ($company): ValueObject {
            /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $userContent */
            $userContent = $searchHit->valueObject;
            $user = $this->userService->loadUser($userContent->id);
            $memberAssignment = $this->getMemberAssignmentByUser($user, $company);

            return $this->domainMapper->mapMember(
                $user,
                $company,
                $this->roleService->loadRoleByIdentifier($memberAssignment->getMemberRole())
            );
        }, $result->searchHits);
    }

    public function countCompanyMembers(
        Company $company,
        ?Criterion $filter = null
    ): int {
        $query = $this->getCompanyMembersQuery(
            $company,
            $filter
        );

        $result = $this->searchService->findContent($query);

        if (null === $result->totalCount) {
            throw new BadStateException('$result->totalCount', 'Unable to count Company Members');
        }

        return $result->totalCount;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause[] $sortClauses
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidCriterionArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     */
    private function getCompanyMembersQuery(
        Company $company,
        ?Criterion $filter = null,
        array $sortClauses = [],
        ?int $limit = 0,
        int $offset = 0
    ): Query {
        $companyCriterion = new Criterion\Subtree(
            $company->getLocationPath()
        );

        $membersCriterion = new ContentTypeIdentifier(
            $this->corporateAccountConfiguration->getMemberContentTypeIdentifier()
        );

        $filter = new LogicalAnd(
            $filter !== null
                ? [$companyCriterion, $membersCriterion, $filter]
                : [$companyCriterion, $membersCriterion]
        );

        return new Query([
            'filter' => $filter,
            'offset' => $offset,
            'limit' => $limit,
            'sortClauses' => $sortClauses,
        ]);
    }

    public function getCompanyContact(Company $company): ?Member
    {
        return $company->getContactId() ? $this->getMember($company->getContactId(), $company) : null;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ForbiddenException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\CorporateAccount\Exception\ValidationFailedExceptionInterface
     */
    public function createMember(
        Company $company,
        MemberCreateStruct $memberCreateStruct,
        Role $role
    ): Member {
        $membersGroupId = $company->getMembersId();
        if ($membersGroupId === null) {
            throw new LogicException('Creating members without members group is not possible');
        }
        $members = $this->userService->loadUserGroup($membersGroupId);

        $this->assertValidCorporateAccountRole($role);

        $user = $this->userService->createUser(
            $memberCreateStruct,
            [$members]
        );

        $this->roleService->assignRoleToUser(
            $role,
            $user,
            new SubtreeLimitation([
                'limitationValues' => [
                    $company->getLocationPath(),
                ],
            ])
        );

        return $this->domainMapper->mapMember(
            $user,
            $company,
            $role
        );
    }

    public function updateMember(Member $member, MemberUpdateStruct $memberUpdateStruct): Member
    {
        return $this->domainMapper->mapMember(
            $this->userService->updateUser(
                $member->getUser(),
                $memberUpdateStruct
            ),
            $member->getCompany(),
            $member->getRole()
        );
    }

    public function deleteMember(Member $member): void
    {
        $this->userService->deleteUser($member->getUser());
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function newMemberCreateStruct(
        string $login,
        string $email,
        string $password,
        ?ContentType $contentType = null
    ): MemberCreateStruct {
        $userCreateStruct = $this->userService->newUserCreateStruct(
            $login,
            $email,
            $password,
            $this->getDefaultLanguageCode(),
            $contentType ?? $this->loadMemberContentType()
        );

        return new MemberCreateStruct([
            'contentType' => $userCreateStruct->contentType,
            'mainLanguageCode' => $userCreateStruct->mainLanguageCode,
            'login' => $userCreateStruct->login,
            'email' => $userCreateStruct->email,
            'password' => $userCreateStruct->password,
            'enabled' => $userCreateStruct->enabled,
            'fields' => $userCreateStruct->fields,
        ]);
    }

    public function newMemberUpdateStruct(): MemberUpdateStruct
    {
        return new MemberUpdateStruct([
            'contentUpdateStruct' => new ContentUpdateStruct(),
        ]);
    }

    private function getDefaultLanguageCode(): string
    {
        $languages = $this->configResolver->getParameter('languages');

        return reset($languages);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    private function loadMemberContentType(): ContentType
    {
        return $this->contentTypeService->loadContentTypeByIdentifier(
            $this->corporateAccountConfiguration->getMemberContentTypeIdentifier()
        );
    }

    /**
     * @throws \Ibexa\Contracts\CorporateAccount\Exception\ValidationFailedExceptionInterface
     */
    private function assertValidCorporateAccountRole(Role $role): void
    {
        $rolesIdentifiers = $this->corporateAccountConfiguration->getCorporateAccountsRolesIdentifiers();
        $validatorBuilder = new MemberRoleValidatorBuilder($this->validator);
        $validatorBuilder->validateRole($role, $rolesIdentifiers);
        $errors = $validatorBuilder->build()->getViolations();
        if ($errors->count() > 0) {
            throw new ValidationFailedException('$role', $errors);
        }
    }
}
