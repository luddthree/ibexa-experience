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

final class TaxonomyEntryUnassignFromContent extends TaxonomyEntryAssignmentUpdate
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
    public function parse(array $data, ParsingDispatcher $parsingDispatcher): Value\TaxonomyEntryUnassignFromContent
    {
        if (!array_key_exists('content', $data)) {
            throw new Exceptions\Parser("Missing 'Content' element for TaxonomyEntryUnassignFromContent.");
        }

        if (!array_key_exists('entries', $data)) {
            throw new Exceptions\Parser("Missing 'Entries' element for TaxonomyEntryUnassignFromContent.");
        }

        return new Value\TaxonomyEntryUnassignFromContent(
            $this->getContent($data['content']),
            $this->getEntries($data['entries'])
        );
    }
}
