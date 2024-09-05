<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Migrations\Currency;

use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\CriterionInterface;
use Ibexa\Migration\ValueObject\Step\StepInterface;

final class CurrencyDeleteStep implements StepInterface
{
    private CriterionInterface $criterion;

    public function __construct(CriterionInterface $criterion)
    {
        $this->criterion = $criterion;
    }

    public function getCriterion(): CriterionInterface
    {
        return $this->criterion;
    }
}
