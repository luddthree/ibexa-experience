<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\View\Matcher\TaxonomyEntryBased;

use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;

final class Level extends AbstractMatcher
{
    private const EXPRESSION_FORMAT = '/^(>=|>|<=|<|=)\s*(\d+)$/i';

    private const OPERATOR_GT = '>';
    private const OPERATOR_GTE = '>=';
    private const OPERATOR_LT = '<';
    private const OPERATOR_LTE = '<=';
    private const OPERATOR_EQ = '=';

    private int $level = 1;

    private string $operator = self::OPERATOR_GT;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    protected function matchTaxonomyEntry(TaxonomyEntry $entry): bool
    {
        switch ($this->operator) {
            case self::OPERATOR_EQ:
                return $entry->level === $this->level;
            case self::OPERATOR_GT:
                return $entry->level > $this->level;
            case self::OPERATOR_GTE:
                return $entry->level >= $this->level;
            case self::OPERATOR_LT:
                return $entry->level < $this->level;
            case self::OPERATOR_LTE:
                return $entry->level <= $this->level;
        }

        throw new InvalidArgumentException('$this->operator', 'Unsupported comparison operator: ' . $this->operator);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function setMatchingConfig($matchingConfig): void
    {
        $expression = $matchingConfig;

        if (is_int($expression)) {
            $this->level = $expression;
            $this->operator = self::OPERATOR_EQ;
        } elseif (is_string($expression) && preg_match(self::EXPRESSION_FORMAT, $expression, $matches) === 1) {
            $this->level = (int)$matches[2];
            $this->operator = $matches[1];
        } else {
            throw new InvalidArgumentException('$matchingConfig', 'comparison expression format');
        }
    }
}
