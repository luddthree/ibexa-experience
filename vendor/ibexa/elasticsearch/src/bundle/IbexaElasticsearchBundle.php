<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Elasticsearch;

use Ibexa\Bundle\Elasticsearch\DependencyInjection\IbexaElasticsearchExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class IbexaElasticsearchBundle extends Bundle
{
    /**
     * Overwritten to be able to use custom alias.
     */
    public function getContainerExtension(): ExtensionInterface
    {
        if (null === $this->extension) {
            $this->extension = new IbexaElasticsearchExtension();
        }

        return $this->extension;
    }
}

class_alias(IbexaElasticsearchBundle::class, 'Ibexa\Platform\Bundle\ElasticSearchEngine\PlatformElasticSearchEngineBundle');
