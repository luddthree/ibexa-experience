<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\StepBuilder;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\ValueObject\Step\StepInterface;

interface StepFactoryInterface
{
    public function getSupportedModes(): array;

    public function create(ValueObject $valueObject, Mode $mode): StepInterface;
}

class_alias(StepFactoryInterface::class, 'Ibexa\Platform\Migration\Generator\StepBuilder\StepFactoryInterface');
