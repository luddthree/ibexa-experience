<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\ParentLocationId;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\Location\Priority;
use Ibexa\Contracts\Core\Repository\Values\Filter\Filter;
use Ibexa\Contracts\CorporateAccount\Exception\CustomerPortalMainPageNotFoundException;
use Ibexa\Contracts\CorporateAccount\Service\CustomerPortalService as CustomerPortalServiceInterface;

final class CustomerPortalService implements CustomerPortalServiceInterface
{
    private ContentService $contentService;

    public function __construct(
        ContentService $contentService
    ) {
        $this->contentService = $contentService;
    }

    public function getMainPage(Location $customerPortalLocation): Content
    {
        $filter = new Filter();
        $filter->withSortClause(new Priority(Query::SORT_DESC));
        $filter->withLimit(1);
        $filter->withCriterion(new ParentLocationId($customerPortalLocation->id));

        $contentList = $this->contentService->find(
            $filter
        );
        if ($contentList->getTotalCount() === 0) {
            throw new CustomerPortalMainPageNotFoundException(
                $customerPortalLocation
            );
        }

        return iterator_to_array($contentList->getIterator())[0];
    }
}
