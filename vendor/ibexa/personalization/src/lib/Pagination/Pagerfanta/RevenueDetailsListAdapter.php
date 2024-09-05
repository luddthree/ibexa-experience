<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Pagination\Pagerfanta;

use Ibexa\Personalization\Value\Performance\Revenue\RevenueDetailsList;
use Pagerfanta\Adapter\AdapterInterface;

final class RevenueDetailsListAdapter implements AdapterInterface
{
    /** @var \Ibexa\Personalization\Value\Performance\Revenue\RevenueDetailsList */
    private $revenueDetailsList;

    public function __construct(RevenueDetailsList $revenueDetailsList)
    {
        $this->revenueDetailsList = $revenueDetailsList;
    }

    public function getNbResults(): int
    {
        return $this->revenueDetailsList->count();
    }

    /**
     * @return \Ibexa\Personalization\Value\Performance\Revenue\RevenueDetails[]
     */
    public function getSlice($offset, $length): array
    {
        return $this->revenueDetailsList->slice($offset, $length);
    }
}

class_alias(RevenueDetailsListAdapter::class, 'Ibexa\Platform\Personalization\Pagination\Pagerfanta\RevenueDetailsListAdapter');
