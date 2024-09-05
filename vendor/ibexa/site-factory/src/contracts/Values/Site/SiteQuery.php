<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\SiteFactory\Values\Site;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\SiteFactory\Values\Query\Criterion\MatchAll;

final class SiteQuery extends ValueObject
{
    public $criteria;

    /** @var int */
    public $limit = 25;

    /** @var int */
    public $offset = 0;

    public function __construct()
    {
        $this->criteria = new MatchAll();
    }
}

class_alias(SiteQuery::class, 'EzSystems\EzPlatformSiteFactory\Values\Site\SiteQuery');
