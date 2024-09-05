<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\SiteFactory\Values\Site;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\SiteFactory\Values\Query\Criterion\MatchAll;

final class PublicAccessQuery extends ValueObject
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion */
    public $criteria;

    public function __construct()
    {
        $this->criteria = new MatchAll();
    }
}

class_alias(PublicAccessQuery::class, 'EzSystems\EzPlatformSiteFactory\Values\Site\PublicAccessQuery');
