<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\DocumentMapper;

use Ibexa\Contracts\Core\Persistence\Content;
use Ibexa\Contracts\Core\Persistence\Content\Location;
use Iterator;

interface DocumentFactoryInterface
{
    public function fromContent(Content $content): Iterator;

    public function fromLocation(Location $location, Content $content = null): Iterator;
}

class_alias(DocumentFactoryInterface::class, 'Ibexa\Platform\ElasticSearchEngine\DocumentMapper\DocumentFactoryInterface');
