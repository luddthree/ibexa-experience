<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion;
use InvalidArgumentException;

/**
 * @extends \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\AbstractAttribute<string>
 */
final class ColorAttribute extends AbstractAttribute
{
    /** @var string[]|null */
    private ?array $value;

    /**
     * @param string[]|null $value
     */
    public function __construct(string $identifier, ?array $value)
    {
        parent::__construct($identifier);
        $this->value = $value;
    }

    /**
     * @return string[]|null
     */
    public function getValue(): ?array
    {
        return $this->value;
    }

    public function getOperator(): string
    {
        return FieldValueCriterion::COMPARISON_IN;
    }

    public function setOperator(string $operator): void
    {
        throw new InvalidArgumentException(
            "Can't set operator. Only " . FieldValueCriterion::class . '::COMPARISON_IN is supported.'
        );
    }
}
