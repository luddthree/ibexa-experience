<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser;

use Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query\Criterion\CriterionProcessor;
use Ibexa\Bundle\ProductCatalog\REST\Value\CatalogCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;
use Ibexa\Contracts\Rest\Exceptions\Parser;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Rest\Input\BaseParser;

/**
 * @template CR of object
 * @template SC of object
 */
final class CatalogCreate extends BaseParser
{
    private const IDENTIFIER_KEY = 'identifier';
    private const CRITERIA_KEY = 'criteria';
    private const NAMES_KEY = 'names';
    private const DESCRIPTIONS_KEY = 'descriptions';
    private const STATUS_KEY = 'status';
    private const CREATOR_ID_KEY = 'creator_id';

    private const OBJECT_KEYS = [
        self::IDENTIFIER_KEY,
        self::CRITERIA_KEY,
        self::NAMES_KEY,
        self::DESCRIPTIONS_KEY,
        self::STATUS_KEY,
        self::CREATOR_ID_KEY,
    ];

    private const REQUIRED_OBJECT_KEYS = [
        self::IDENTIFIER_KEY,
        self::CRITERIA_KEY,
        self::NAMES_KEY,
        self::STATUS_KEY,
    ];

    /** @var \Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query\Criterion\CriterionProcessor<CR,SC> */
    private CriterionProcessor $criterionProcessor;

    /** @param \Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query\Criterion\CriterionProcessor<CR,SC> $criterionProcessor */
    public function __construct(CriterionProcessor $criterionProcessor)
    {
        $this->criterionProcessor = $criterionProcessor;
    }

    /**
     * @phpstan-param array<mixed> $data
     */
    public function parse(array $data, ParsingDispatcher $parsingDispatcher): CatalogCreateStruct
    {
        $inputKeys = array_keys($data);
        $missingKeys = array_diff(self::REQUIRED_OBJECT_KEYS, $inputKeys);
        $redundantKeys = array_diff($inputKeys, self::OBJECT_KEYS);

        if (!empty($missingKeys)) {
            throw new Parser(
                sprintf(
                    'Missing properties (%s) for Catalog.',
                    implode(', ', $missingKeys)
                )
            );
        }

        if (!empty($redundantKeys)) {
            throw new Parser(
                sprintf(
                    'The following properties for Catalog are redundant: %s.',
                    implode(', ', $redundantKeys)
                )
            );
        }

        $criteria = $data[self::CRITERIA_KEY];
        if (!is_array($criteria)) {
            throw new Parser('The "criteria" parameter must be an array.');
        }

        $names = $data[self::NAMES_KEY];
        if (!is_array($names)) {
            throw new Parser('The "names" parameter must be an array.');
        }

        $descriptions = $data[self::DESCRIPTIONS_KEY] ?? [];
        if (!is_array($descriptions)) {
            throw new Parser('The "descriptions" parameter must be an array.');
        }

        return new CatalogCreateStruct(
            $data[self::IDENTIFIER_KEY],
            $this->getProcessedCriteria($criteria, $parsingDispatcher),
            $names,
            $descriptions,
            $data[self::STATUS_KEY],
            isset($data[self::CREATOR_ID_KEY]) ? (int)$data[self::CREATOR_ID_KEY] : null
        );
    }

    /**
     * @param array<string, array<mixed>> $criteria
     */
    private function getProcessedCriteria(
        array $criteria,
        ParsingDispatcher $parsingDispatcher
    ): CriterionInterface {
        /** @var array<\Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface> $processedCriteria */
        $processedCriteria = $this->criterionProcessor->processCriteriaArray($criteria, $parsingDispatcher);

        return new LogicalAnd($processedCriteria);
    }
}
