<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\ElasticSearch\Index\Group;

use Ibexa\Contracts\Elasticsearch\ElasticSearch\Index\Group\GroupResolverInterface;
use Ibexa\Contracts\Elasticsearch\Mapping\BaseDocument;

final class LanguageGroupResolver implements GroupResolverInterface
{
    private const SEGMENT_SEPARATOR = '_';

    public function resolveDocumentGroup(BaseDocument $document): string
    {
        return str_replace('-', self::SEGMENT_SEPARATOR, strtolower($document->languageCode));
    }
}
