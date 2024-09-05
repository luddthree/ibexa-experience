<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\CriterionGenerator;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;

/**
 * Creates a specific Criterion object with specified value.
 */
interface GeneratorInterface
{
    /**
     * @param mixed $value
     */
    public function generate($value): Criterion;
}

class_alias(GeneratorInterface::class, 'Ibexa\Platform\Migration\Generator\CriterionGenerator\GeneratorInterface');
