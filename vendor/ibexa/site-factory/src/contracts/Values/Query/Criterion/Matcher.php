<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Contracts\SiteFactory\Values\Query\Criterion;

use Ibexa\Contracts\SiteFactory\Values\Query\Criterion;

abstract class Matcher extends Criterion
{
}

class_alias(Matcher::class, 'EzSystems\EzPlatformSiteFactory\Values\Query\Criterion\Matcher');
