<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\SiteFactory\Persistence\PublicAccess\Query\Criterion;

use Ibexa\SiteFactory\Persistence\Site\Query\Criterion;

abstract class Matcher extends Criterion\MatchAll
{
}

class_alias(Matcher::class, 'EzSystems\EzPlatformSiteFactory\Persistence\PublicAccess\Query\Criterion\Matcher');
