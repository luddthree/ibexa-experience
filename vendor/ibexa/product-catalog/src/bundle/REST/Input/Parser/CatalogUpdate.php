<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser;

use Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query\Criterion\CriterionProcessor;
use Ibexa\Bundle\ProductCatalog\REST\Value\CatalogUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;
use Ibexa\Contracts\Rest\Exceptions\Parser;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Rest\Input\BaseParser;

/**
 * @template CR of object
 * @template SC of object
 */
final class CatalogUpdate extends BaseParser
{
    private const IDENTIFIER_KEY = 'identifier';
    private const TRANSITION_KEY = 'transition';
    private const CRITERIA_KEY = 'criteria';
    private const NAMES_KEY = 'names';
    private const DESCRIPTIONS_KEY = 'descriptions';

    private const OBJECT_KEYS = [
        self::IDENTIFIER_KEY,
        self::TRANSITION_KEY,
        self::CRITERIA_KEY,
        self::NAMES_KEY,
        self::DESCRIPTIONS_KEY,
    ];

    /** @var CriterionProcessor<CR, SC> */
    private CriterionProcessor $criterionProcessor;

    /** @param CriterionProcessor<CR, SC> $criterionProcessor */
    public function __construct(CriterionProcessor $criterionProcessor)
    {
        $this->criterionProcessor = $criterionProcessor;
    }

    /**
     * @phpstan-param array<mixed> $data
     */
    public function parse(array $data, ParsingDispatcher $parsingDispatcher): CatalogUpdateStruct
    {
        $inputKeys = array_keys($data);
        $redundantKeys = array_diff($inputKeys, self::OBJECT_KEYS);

        if (!empty($redundantKeys)) {
            throw new Parser(
                sprintf(
                    'The following properties for Catalog are redundant: %s.',
                    implode(', ', $redundantKeys)
                )
            );
        }

        $transition = $data[self::TRANSITION_KEY] ?? null;
        $identifier = $data[self::IDENTIFIER_KEY] ?? null;
        $names = $data[self::NAMES_KEY] ?? [];
        $descriptions = $data[self::DESCRIPTIONS_KEY] ?? [];

        $criteria = isset($data[self::CRITERIA_KEY])
            ? $this->getProcessedCriteria($data[self::CRITERIA_KEY], $parsingDispatcher)
            : null;

        return new CatalogUpdateStruct(
            $transition,
            $identifier,
            $criteria,
            $names,
            $descriptions
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
