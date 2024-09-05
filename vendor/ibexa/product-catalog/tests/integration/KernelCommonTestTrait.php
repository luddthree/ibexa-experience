<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog;

use Ibexa\Solr\Handler as SolrHandler;

trait KernelCommonTestTrait
{
    protected static function postLoadFixtures(): void
    {
        self::ensureSearchIndexIsUpdated();
    }

    public static function ensureSearchIndexIsUpdated(): void
    {
        if (getenv('SEARCH_ENGINE') === 'solr') {
            $handler = self::getServiceByClassName(SolrHandler::class, SolrHandler::class);
            $handler->commit();
        }
    }
}
