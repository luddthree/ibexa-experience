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

final class TaxonomyEntryMove extends BaseParser
{
    private TaxonomyServiceInterface $taxonomyService;

    public function __construct(
        TaxonomyServiceInterface $taxonomyService
    ) {
        $this->taxonomyService = $taxonomyService;
    }

    /**
     * @phpstan-param array{
     *     entry: int,
     *     sibling: int,
     *     position: \Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface::MOVE_POSITION_*,
     * } $data
     */
    public function parse(array $data, ParsingDispatcher $parsingDispatcher): Value\TaxonomyEntryMove
    {
        if (!array_key_exists('entry', $data)) {
            throw new Exceptions\Parser("Missing 'Entry' element for TaxonomyEntryMove.");
        }

        if (!array_key_exists('sibling', $data)) {
            throw new Exceptions\Parser("Missing 'Sibling' element for TaxonomyEntryMove.");
        }

        if (!array_key_exists('position', $data)) {
            throw new Exceptions\Parser("Missing 'Position' element for TaxonomyEntryMove.");
        }

        $entry = $this->taxonomyService->loadEntryById($data['entry']);
        $sibling = $this->taxonomyService->loadEntryById($data['sibling']);

        return new Value\TaxonomyEntryMove(
            $entry,
            $sibling,
            $data['position']
        );
    }
}
