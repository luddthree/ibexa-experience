<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\Query\ResultExtractor\FacetResultExtractor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\FacetBuilder;

final class TestFacetBuilder extends FacetBuilder
{
}

class_alias(TestFacetBuilder::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\Query\ResultExtractor\FacetResultExtractor\TestFacetBuilder');
