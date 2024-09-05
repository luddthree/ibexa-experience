<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\StepBuilder;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Migration\ValueObject\Step\StepInterface;

interface StepBuilderInterface
{
    public function build(ValueObject $valueObject): StepInterface;
}

class_alias(StepBuilderInterface::class, 'Ibexa\Platform\Migration\Generator\StepBuilder\StepBuilderInterface');
