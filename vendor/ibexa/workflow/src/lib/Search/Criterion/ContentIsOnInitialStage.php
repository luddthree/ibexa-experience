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

final class ContentIsOnInitialStage extends Criterion
{
    public function __construct(bool $negation = false)
    {
        parent::__construct(null, null, $negation);
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
                Specifications::TYPE_BOOLEAN
            ),
        ];
    }
}

class_alias(ContentIsOnInitialStage::class, 'EzSystems\EzPlatformWorkflow\Search\Criterion\ContentIsOnInitialStage');
