<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Search\Criterion;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator\Specifications;

final class OriginatedByUserId extends Criterion
{
    public function __construct(int $userId)
    {
        parent::__construct(null, null, $userId);
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator\Specifications[]
     */
    public function getSpecifications(): array
    {
        return [
            new Specifications(
                Operator::EQ,
                Specifications::FORMAT_SINGLE,
                Specifications::TYPE_INTEGER
            ),
            new Specifications(
                Operator::IN,
                Specifications::FORMAT_ARRAY,
                Specifications::TYPE_INTEGER
            ),
        ];
    }
}

class_alias(OriginatedByUserId::class, 'EzSystems\EzPlatformWorkflow\Search\Criterion\OriginatedByUserId');
