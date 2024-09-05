<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\CriterionGenerator;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;

final class UserLoginGenerator implements GeneratorInterface
{
    public function generate($value): Criterion
    {
        return new Criterion\UserLogin($value);
    }
}

class_alias(UserLoginGenerator::class, 'Ibexa\Platform\Migration\Generator\CriterionGenerator\UserLoginGenerator');
