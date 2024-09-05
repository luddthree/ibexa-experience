<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Connector\Dam\Search;

use Symfony\Component\HttpFoundation\Request;

interface QueryFactory
{
    public function buildFromRequest(Request $request): Query;
}

class_alias(QueryFactory::class, 'Ibexa\Platform\Contracts\Connector\Dam\Search\QueryFactory');
