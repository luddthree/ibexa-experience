<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Elasticsearch\Repository\Values\Content\Query;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator\Specifications;

final class Pinned extends Criterion
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion */
    private $organicCriteria;

    public function __construct(array $value, Criterion $organicCriteria)
    {
        parent::__construct(null, null, $value);

        $this->organicCriteria = $organicCriteria;
    }

    public function getSpecifications(): array
    {
        return [
            new Specifications(Operator::IN, Specifications::FORMAT_ARRAY),
        ];
    }

    public function getOrganicCriteria(): Criterion
    {
        return $this->organicCriteria;
    }
}

class_alias(Pinned::class, 'Ibexa\Platform\Contracts\ElasticSearchEngine\Repository\Values\Content\Query\Pinned');
