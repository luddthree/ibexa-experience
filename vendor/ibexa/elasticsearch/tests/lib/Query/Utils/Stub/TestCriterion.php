<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\Query\Utils\Stub;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Value;

final class TestCriterion extends Criterion
{
    public function __construct(?string $target, ?string $operator, $value, ?Value $valueData = null)
    {
        /* Intentionally skip parent constructor call */

        $this->target = $target;
        $this->operator = $operator;
        $this->value = $value;
        $this->valueData = $valueData;
    }

    public function getSpecifications(): array
    {
        return [];
    }
}

class_alias(TestCriterion::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\Query\Utils\Stub\TestCriterion');
