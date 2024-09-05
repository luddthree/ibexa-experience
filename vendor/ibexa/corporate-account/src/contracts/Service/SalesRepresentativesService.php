<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\CorporateAccount\Service;

use Ibexa\Contracts\CorporateAccount\Values\SalesRepresentativesList;

interface SalesRepresentativesService
{
    public const DEFAULT_PAGE_LIMIT = 25;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException if configured provider has missing
     *          required data, e.g. by default Sales Representatives User Group
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException if other misconfiguration error occurred
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function getAll(int $offset = 0, int $limit = self::DEFAULT_PAGE_LIMIT): SalesRepresentativesList;
}
