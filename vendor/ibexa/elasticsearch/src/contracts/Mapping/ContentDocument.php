<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Elasticsearch\Mapping;

final class ContentDocument extends BaseDocument
{
}

class_alias(ContentDocument::class, 'Ibexa\Platform\Contracts\ElasticSearchEngine\Mapping\ContentDocument');
