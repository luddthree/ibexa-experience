<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Elasticsearch\Mapping;

use Ibexa\Contracts\Core\Search\Document;

abstract class BaseDocument extends Document
{
    /** @var int */
    public $contentTypeId;
}

class_alias(BaseDocument::class, 'Ibexa\Platform\Contracts\ElasticSearchEngine\Mapping\BaseDocument');
