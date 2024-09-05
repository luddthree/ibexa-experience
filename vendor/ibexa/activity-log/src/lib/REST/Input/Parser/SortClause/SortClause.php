<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\REST\Input\Parser\SortClause;

use Ibexa\ActivityLog\REST\Input\Parser\AbstractParser;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\SortClause\SortClauseInterface;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use LogicException;

final class SortClause extends AbstractParser
{
    public const MEDIA_TYPE = 'application/vnd.ibexa.api.internal.activity_log.sort_clause';

    protected function getName(): string
    {
        return 'sort_clause';
    }

    protected function normalize(array $data): array
    {
        if (isset($data['_type']) && !isset($data['type'])) {
            $data['type'] = $data['_type'];
            unset($data['_type']);
        }

        return parent::normalize($data);
    }

    public function innerParse(array $data, ParsingDispatcher $parsingDispatcher): SortClauseInterface
    {
        $type = $data['type'];
        $mediaType = sprintf('%s.%s', self::MEDIA_TYPE, $type);
        $result = $parsingDispatcher->parse($data, $mediaType);

        if (!$result instanceof SortClauseInterface) {
            throw new LogicException(sprintf(
                'Invalid result from parser. Expected %s, received %s. Check parser handling %s media type.',
                SortClauseInterface::class,
                get_debug_type($result),
                $mediaType,
            ));
        }

        return $result;
    }

    protected function getMandatoryKeys(): array
    {
        return ['type'];
    }
}
