<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Migrations\Currency;

use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\CriterionInterface;
use Ibexa\Migration\ValueObject\Step\StepInterface;

final class CurrencyUpdateStep implements StepInterface
{
    private CriterionInterface $criterion;

    /** @phpstan-var non-empty-string|null */
    private ?string $code;

    /** @phpstan-var int<0, max>|null */
    private ?int $subunits;

    private ?bool $enabled;

    /**
     * @phpstan-param non-empty-string|null $code
     * @phpstan-param int<0, max>|null $subunits
     */
    public function __construct(CriterionInterface $criterion, ?string $code, ?int $subunits, ?bool $enabled)
    {
        $this->criterion = $criterion;
        $this->code = $code;
        $this->subunits = $subunits;
        $this->enabled = $enabled;
    }

    public function getCriterion(): CriterionInterface
    {
        return $this->criterion;
    }

    /**
     * @phpstan-return non-empty-string|null
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @phpstan-return int<0, max>|null
     */
    public function getSubunits(): ?int
    {
        return $this->subunits;
    }

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }
}
