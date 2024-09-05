<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\Query\ResultExtractor\FacetResultExtractor;

use Ibexa\Contracts\Core\Repository\Values\Content\Search\Facet;

final class TestFacet extends Facet
{
}

class_alias(TestFacet::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\Query\ResultExtractor\FacetResultExtractor\TestFacet');
