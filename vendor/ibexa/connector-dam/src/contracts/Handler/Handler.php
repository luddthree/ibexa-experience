<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Connector\Dam\Handler;

use Ibexa\Contracts\Connector\Dam\Asset;
use Ibexa\Contracts\Connector\Dam\Search\AssetSearchResult;
use Ibexa\Contracts\Connector\Dam\Search\Query;

interface Handler
{
    public function search(Query $query, int $offset = 0, int $limit = 20): AssetSearchResult;

    public function fetchAsset(string $id): Asset;
}

class_alias(Handler::class, 'Ibexa\Platform\Contracts\Connector\Dam\Handler\Handler');
