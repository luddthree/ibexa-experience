<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query;

use Ibexa\Bundle\ProductCatalog\REST\Value\RestViewInput;
use Ibexa\Contracts\Rest\Exceptions;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Rest\Server\Input\Parser\Criterion as CriterionParser;

abstract class AbstractViewInput extends CriterionParser
{
    abstract protected function getViewInputIdentifier(): string;

    /**
     * @param array<mixed> $data
     *
     * @throws \Ibexa\Contracts\Rest\Exceptions\Parser
     */
    public function parse(array $data, ParsingDispatcher $parsingDispatcher): RestViewInput
    {
        $restViewInput = new RestViewInput();

        if (!array_key_exists('identifier', $data)) {
            throw new Exceptions\Parser('Missing <identifier> attribute for <ViewInput>.');
        }

        $restViewInput->identifier = $data['identifier'];
        $restViewInput->languageCode = $data['languageCode'] ?? null;

        $viewInputIdentifier = $this->getViewInputIdentifier();
        if (!array_key_exists($viewInputIdentifier, $data)) {
            throw new Exceptions\Parser('Missing ' . $viewInputIdentifier . ' attribute for <ViewInput>.');
        }

        if (!is_array($data[$viewInputIdentifier])) {
            throw new Exceptions\Parser($viewInputIdentifier . ' attribute for <ViewInput> contains invalid data.');
        }

        $queryData = $data[$viewInputIdentifier];
        $queryMediaType = 'application/vnd.ibexa.api.internal.' . $viewInputIdentifier;
        $restViewInput->query = $parsingDispatcher->parse($queryData, $queryMediaType);

        return $restViewInput;
    }
}
