<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\Query\Utils;

use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Elasticsearch\Query\QueryContext;

final class MockUtils
{
    public static function createEmptyLanguageFilter(): LanguageFilter
    {
        return new LanguageFilter([], false, false);
    }

    public static function createEmptyQueryContext(): QueryContext
    {
        return new QueryContext(new Query(), self::createEmptyLanguageFilter());
    }
}

class_alias(MockUtils::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\Query\Utils\MockUtils');
