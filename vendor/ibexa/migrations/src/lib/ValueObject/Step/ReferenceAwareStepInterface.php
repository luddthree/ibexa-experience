<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Step;

interface ReferenceAwareStepInterface extends StepInterface
{
    /** @return \Ibexa\Migration\Generator\Reference\ReferenceDefinition[] */
    public function getReferences(): array;
}

class_alias(ReferenceAwareStepInterface::class, 'Ibexa\Platform\Migration\ValueObject\Step\ReferenceAwareStepInterface');
