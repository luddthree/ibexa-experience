<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser;

use Ibexa\Contracts\Rest\Exceptions\Parser;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Rest\Input\BaseParser;

/**
 * @template T of object
 */
abstract class AbstractParser extends BaseParser
{
    /**
     * @param array<mixed> $data
     *
     * @return T
     */
    public function parse(array $data, ParsingDispatcher $parsingDispatcher)
    {
        $inputKeys = array_keys($data);

        $missingKeys = array_diff($this->getRequiredKeys(), $inputKeys);
        if (!empty($missingKeys)) {
            throw new Parser(
                sprintf(
                    'Missing properties (%s) for %s.',
                    implode(', ', $missingKeys),
                    $this->getIdentifier()
                )
            );
        }

        $redundantKeys = array_diff($inputKeys, array_merge($this->getRequiredKeys(), $this->getOptionalKeys()));
        if (!empty($redundantKeys)) {
            throw new Parser(
                sprintf(
                    'The following properties for %s are redundant: %s.',
                    $this->getIdentifier(),
                    implode(', ', $redundantKeys)
                )
            );
        }

        return $this->doParse($data, $parsingDispatcher);
    }

    /**
     * @return array<string>
     */
    public function getOptionalKeys(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    public function getRequiredKeys(): array
    {
        return [];
    }

    abstract public function getIdentifier(): string;

    /**
     * @param array<mixed> $data
     *
     * @return T
     */
    abstract public function doParse(array $data, ParsingDispatcher $parsingDispatcher);
}
