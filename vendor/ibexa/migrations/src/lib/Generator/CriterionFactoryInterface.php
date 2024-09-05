<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;

interface CriterionFactoryInterface
{
    /**
     * @param array<mixed> $value
     */
    public function build(?string $matchProperty, array $value = []): Criterion;
}

class_alias(CriterionFactoryInterface::class, 'Ibexa\Platform\Migration\Generator\CriterionFactoryInterface');
