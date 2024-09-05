<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Step;

trait ReferenceAwareStepTrait
{
    /** @var \Ibexa\Migration\Generator\Reference\ReferenceDefinition[] */
    public $references;

    /**
     * @return \Ibexa\Migration\Generator\Reference\ReferenceDefinition[]
     */
    public function getReferences(): array
    {
        return $this->references;
    }
}

class_alias(ReferenceAwareStepTrait::class, 'Ibexa\Platform\Migration\ValueObject\Step\ReferenceAwareStepTrait');
