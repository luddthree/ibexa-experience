<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\ElasticSearch\Client;

use Ibexa\Elasticsearch\ElasticSearch\Client\Config\Client as ClientConfig;

interface ClientConfigurationProviderInterface
{
    public function getClientConfiguration(?string $name = null): ClientConfig;
}

class_alias(ClientConfigurationProviderInterface::class, 'Ibexa\Platform\ElasticSearchEngine\ElasticSearch\Client\ClientConfigurationProviderInterface');
