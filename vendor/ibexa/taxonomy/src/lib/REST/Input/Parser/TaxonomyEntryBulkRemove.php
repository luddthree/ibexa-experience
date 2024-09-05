<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\REST\Input\Parser;

use Ibexa\Contracts\Rest\Exceptions;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Rest\Input\BaseParser;
use Ibexa\Taxonomy\REST\Input\Value;

final class TaxonomyEntryBulkRemove extends BaseParser
{
    private TaxonomyServiceInterface $taxonomyService;

    public function __construct(
        TaxonomyServiceInterface $taxonomyService
    ) {
        $this->taxonomyService = $taxonomyService;
    }

    /**
     * @phpstan-param array{
     *     entries: int[],
     * } $data
     */
    public function parse(array $data, ParsingDispatcher $parsingDispatcher): Value\TaxonomyEntryBulkRemove
    {
        if (!array_key_exists('entries', $data)) {
            throw new Exceptions\Parser("Missing 'Entries' element for TaxonomyEntryBulkRemove");
        }

        $entries = [];
        foreach ($data['entries'] as $entryId) {
            $entries[] = $this->taxonomyService->loadEntryById($entryId);
        }

        return new Value\TaxonomyEntryBulkRemove($entries);
    }
}
