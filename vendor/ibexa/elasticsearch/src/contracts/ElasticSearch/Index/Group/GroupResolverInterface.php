<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Elasticsearch\ElasticSearch\Index\Group;

use Ibexa\Contracts\Elasticsearch\Mapping\BaseDocument;

interface GroupResolverInterface
{
    public function resolveDocumentGroup(BaseDocument $document): string;
}
