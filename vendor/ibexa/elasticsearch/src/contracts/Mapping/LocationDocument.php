<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Elasticsearch\Mapping;

final class LocationDocument extends BaseDocument
{
}

class_alias(LocationDocument::class, 'Ibexa\Platform\Contracts\ElasticSearchEngine\Mapping\LocationDocument');
