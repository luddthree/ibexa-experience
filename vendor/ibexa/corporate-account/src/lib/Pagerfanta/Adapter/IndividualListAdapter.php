<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Pagerfanta\Adapter;

use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\CorporateAccount\Configuration\CorporateAccountConfiguration;

class IndividualListAdapter extends ContentListAdapter
{
    private ConfigResolverInterface $configResolver;

    /** @var \Ibexa\Contracts\Core\Repository\UserService */
    private UserService $userService;

    public function __construct(
        SearchService $searchService,
        CorporateAccountConfiguration $corporateAccount,
        array $criteria,
        ConfigResolverInterface $configResolver,
        UserService $userService
    ) {
        parent::__construct($searchService, $corporateAccount, $criteria);

        $this->configResolver = $configResolver;
        $this->userService = $userService;
    }

    protected function getContentTypeIdentifier(): string
    {
        return $this->configResolver->getParameter(
            'user_registration.user_type_identifier',
            null,
            'site_group'
        );
    }

    public function getSlice(
        $offset,
        $length
    ): iterable {
        $userContentList = parent::getSlice($offset, $length);

        /** @var \Ibexa\Core\Repository\Values\Content\Content $userContent */
        foreach ($userContentList as $userContent) {
            yield $this->userService->loadUser($userContent->id);
        }
    }
}
