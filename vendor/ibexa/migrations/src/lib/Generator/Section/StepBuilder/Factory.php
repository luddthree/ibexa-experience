<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\Section\StepBuilder;

use Ibexa\Contracts\Core\Repository\Values\Content\Section;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\Generator\StepBuilder\AbstractStepFactory;
use Webmozart\Assert\Assert;

final class Factory extends AbstractStepFactory
{
    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\ValueObject|\Ibexa\Contracts\Core\Repository\Values\Content\Section $section
     */
    protected function prepareLogMessage(ValueObject $section, Mode $mode, string $type): ?string
    {
        Assert::isInstanceOf($section, Section::class);

        return sprintf('[Step] Preparing section create for %s', $section->name);
    }
}

class_alias(Factory::class, 'Ibexa\Platform\Migration\Generator\Section\StepBuilder\Factory');
