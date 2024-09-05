<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\REST\Input\Parser;

use Ibexa\Contracts\Rest\Exceptions;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Taxonomy\REST\Input\Value;

final class TaxonomyEntryAssignToContent extends TaxonomyEntryAssignmentUpdate
{
    /**
     * @phpstan-param array{
     *     'content': int,
     *     'entries': array<int>
     * } $data
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function parse(array $data, ParsingDispatcher $parsingDispatcher): Value\TaxonomyEntryAssignToContent
    {
        if (!array_key_exists('content', $data)) {
            throw new Exceptions\Parser("Missing 'Content' element for TaxonomyEntryAssignToContent.");
        }

        if (!array_key_exists('entries', $data)) {
            throw new Exceptions\Parser("Missing 'Entries' element for TaxonomyEntryAssignToContent.");
        }

        return new Value\TaxonomyEntryAssignToContent(
            $this->getContent($data['content']),
            $this->getEntries($data['entries'])
        );
    }
}
