<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\Controller\REST;

use Ibexa\Contracts\CorporateAccount\Service\SalesRepresentativesService;
use Ibexa\CorporateAccount\REST\Value\SalesRepresentativesList;
use Ibexa\Rest\Server\Controller as RestController;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
final class SalesRepresentativesController extends RestController
{
    private SalesRepresentativesService $salesRepresentativesService;

    public function __construct(SalesRepresentativesService $salesRepresentativesService)
    {
        $this->salesRepresentativesService = $salesRepresentativesService;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function getAllAction(Request $request): SalesRepresentativesList
    {
        $offset = (int)$request->query->get('offset');
        $limit = $request->query->has('limit')
            ? (int)$request->query->get('limit')
            : SalesRepresentativesService::DEFAULT_PAGE_LIMIT;

        return new SalesRepresentativesList($this->salesRepresentativesService->getAll($offset, $limit));
    }
}
