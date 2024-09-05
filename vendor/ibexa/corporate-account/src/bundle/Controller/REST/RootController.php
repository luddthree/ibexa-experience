<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\Controller\REST;

use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\RoleService;
use Ibexa\CorporateAccount\Configuration\CorporateAccountConfiguration;
use Ibexa\CorporateAccount\REST\Value\CorporateAccountRoot;
use Ibexa\CorporateAccount\REST\Value\Link;
use Ibexa\Rest\Server\Controller as RestController;

/**
 * @internal
 */
final class RootController extends RestController
{
    private const MEDIA_TYPE_REPOSITORY_CONTENT_TYPE = 'ContentType';
    private const IBEXA_REST_ROUTE_LOAD_CONTENT_TYPE = 'ibexa.rest.load_content_type';

    private CorporateAccountConfiguration $corporateAccountConfiguration;

    private ContentTypeService $contentTypeService;

    private RoleService $roleService;

    public function __construct(
        CorporateAccountConfiguration $corporateAccountConfiguration,
        ContentTypeService $contentTypeService,
        RoleService $roleService
    ) {
        $this->corporateAccountConfiguration = $corporateAccountConfiguration;
        $this->contentTypeService = $contentTypeService;
        $this->roleService = $roleService;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function getRootAction(): CorporateAccountRoot
    {
        $links = [
            new Link('ca-companies', 'CompaniesList', 'ibexa.rest.corporate_account.companies.list'),
            new Link(
                'ca-types',
                'ContentTypeGroup',
                'ibexa.rest.load_content_type_group_list',
                [
                    'identifier' => $this->corporateAccountConfiguration->getContentTypeGroupIdentifier(),
                ]
            ),
            $this->buildCorporateAccountContentTypeLink(
                'ca-types-company',
                $this->corporateAccountConfiguration->getCompanyContentTypeIdentifier()
            ),
            $this->buildCorporateAccountContentTypeLink(
                'ca-types-member',
                $this->corporateAccountConfiguration->getMemberContentTypeIdentifier()
            ),
            $this->buildCorporateAccountContentTypeLink(
                'ca-types-address',
                $this->corporateAccountConfiguration->getShippingAddressContentTypeIdentifier()
            ),
            new Link(
                'ca-sales-representatives',
                'SalesRepresentativesList',
                'ibexa.rest.corporate_account.sales_representatives.get'
            ),
        ];

        $links = $this->appendCorporateAccountRolesLinks($links);

        return new CorporateAccountRoot($links);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    private function getContentTypeID(string $contentTypeIdentifier): int
    {
        return $this->contentTypeService->loadContentTypeByIdentifier($contentTypeIdentifier)->id;
    }

    /**
     * @phpstan-param non-empty-string $rel
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    private function buildCorporateAccountContentTypeLink(
        string $rel,
        string $contentTypeIdentifier
    ): Link {
        return new Link(
            $rel,
            self::MEDIA_TYPE_REPOSITORY_CONTENT_TYPE,
            self::IBEXA_REST_ROUTE_LOAD_CONTENT_TYPE,
            [
                'contentTypeId' => $this->getContentTypeID($contentTypeIdentifier),
            ]
        );
    }

    /**
     * @param array<\Ibexa\CorporateAccount\REST\Value\Link> $links
     *
     * @return array<\Ibexa\CorporateAccount\REST\Value\Link>
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    private function appendCorporateAccountRolesLinks(array $links): array
    {
        $rolesIdentifiers = $this->corporateAccountConfiguration
            ->getCorporateAccountsRolesIdentifiers();

        foreach ($rolesIdentifiers as $roleKey => $roleIdentifier) {
            $links[] = new Link(
                "ca-role-$roleKey",
                'Role',
                'ibexa.rest.load_role',
                [
                    'roleId' => $this->getRoleIdFromIdentifier($roleIdentifier),
                ]
            );
        }

        return $links;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    private function getRoleIdFromIdentifier(string $roleIdentifier): int
    {
        return $this->roleService->loadRoleByIdentifier($roleIdentifier)->id;
    }
}
