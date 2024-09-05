<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Form\Processor;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\RoleService;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\CorporateAccount\Service\CompanyService;
use Ibexa\Contracts\CorporateAccount\Service\MemberService;
use Ibexa\Contracts\CorporateAccount\Service\ShippingAddressService;
use Ibexa\CorporateAccount\Values\Mapper\DomainMapperInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

abstract class FormProcessor implements EventSubscriberInterface
{
    protected ContentService $contentService;

    protected UserService $userService;

    protected RoleService $roleService;

    protected UrlGeneratorInterface $urlGenerator;

    protected DomainMapperInterface $domainMapper;

    protected CompanyService $companyService;

    protected MemberService $memberService;

    protected ShippingAddressService $shippingAddressService;

    public function __construct(
        ContentService $contentService,
        UserService $userService,
        RoleService $roleService,
        UrlGeneratorInterface $urlGenerator,
        DomainMapperInterface $domainMapper,
        CompanyService $companyService,
        MemberService $memberService,
        ShippingAddressService $shippingAddressService
    ) {
        $this->contentService = $contentService;
        $this->userService = $userService;
        $this->roleService = $roleService;
        $this->urlGenerator = $urlGenerator;
        $this->domainMapper = $domainMapper;
        $this->companyService = $companyService;
        $this->memberService = $memberService;
        $this->shippingAddressService = $shippingAddressService;
    }

    /**
     * @param \Ibexa\ContentForms\Data\Content\ContentCreateData|\Ibexa\ContentForms\Data\Content\ContentUpdateData $data
     */
    protected function resolveMainLanguageCode($data): string
    {
        if ($data->isNew()) {
            /** @var \Ibexa\ContentForms\Data\Content\ContentCreateData $data */
            return $data->mainLanguageCode;
        }

        /** @var \Ibexa\ContentForms\Data\Content\ContentUpdateData $data */
        return $data->contentDraft->getVersionInfo()->getContentInfo()->mainLanguageCode;
    }
}
