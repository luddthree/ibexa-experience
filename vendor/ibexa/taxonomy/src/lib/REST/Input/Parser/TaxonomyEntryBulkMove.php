<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\REST\Input\Parser;

use Ibexa\Contracts\Rest\Exceptions;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Rest\Input\BaseParser;
use Ibexa\Taxonomy\REST\Input\Value;

final class TaxonomyEntryBulkMove extends BaseParser
{
    /**
     * @phpstan-param array{
     *     entries: array{array{
     *          entry: int,
     *          sibling: int,
     *          position: \Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface::MOVE_POSITION_*
     *     }},
     * } $data
     */
    public function parse(array $data, ParsingDispatcher $parsingDispatcher): Value\TaxonomyEntryBulkMove
    {
        $entries = [];

        if (!array_key_exists('entries', $data)) {
            throw new Exceptions\Parser("Missing 'Entries' element for TaxonomyEntryBulkMove.");
        }

        foreach ($data['entries'] as $entry) {
            /** @var \Ibexa\Taxonomy\REST\Input\Value\TaxonomyEntryMove $moveEntryInput */
            $moveEntryInput = $parsingDispatcher->parse(
                $entry,
                'application/vnd.ibexa.api.TaxonomyEntryMove'
            );
            $entries[] = $moveEntryInput;
        }

        return new Value\TaxonomyEntryBulkMove($entries);
    }
}
