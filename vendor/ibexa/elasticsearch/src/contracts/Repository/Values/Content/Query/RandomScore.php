<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Elasticsearch\Repository\Values\Content\Query;

use Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;

final class RandomScore extends Criterion
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion */
    private $criterion;

    /** @var int|null */
    private $seed;

    public function __construct(Criterion $criterion, ?int $seed = null)
    {
        $this->criterion = $criterion;
        $this->seed = $seed;
    }

    public function getSpecifications(): array
    {
        throw new NotImplementedException('getSpecifications() not implemented for RandomScore');
    }

    public function getCriterion(): Criterion
    {
        return $this->criterion;
    }

    public function getSeed(): ?int
    {
        return $this->seed;
    }

    public function hasSeed(): bool
    {
        return $this->seed !== null;
    }
}

class_alias(RandomScore::class, 'Ibexa\Platform\Contracts\ElasticSearchEngine\Repository\Values\Content\Query\RandomScore');
