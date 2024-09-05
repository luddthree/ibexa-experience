<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\ElasticSearch\Client;

use Elasticsearch\Client;

interface ClientFactoryInterface
{
    public function create(?string $name = null): Client;
}

class_alias(ClientFactoryInterface::class, 'Ibexa\Platform\ElasticSearchEngine\ElasticSearch\Client\ClientFactoryInterface');
