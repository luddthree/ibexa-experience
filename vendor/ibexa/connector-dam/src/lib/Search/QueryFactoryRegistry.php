<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Connector\Dam\Search;

use Ibexa\Contracts\Connector\Dam\AssetSource;
use Ibexa\Contracts\Connector\Dam\Search\QueryFactory;

interface QueryFactoryRegistry
{
    public function getFactory(AssetSource $source): QueryFactory;
}

class_alias(QueryFactoryRegistry::class, 'Ibexa\Platform\Connector\Dam\Search\QueryFactoryRegistry');
