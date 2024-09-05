<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Step;

use Ibexa\Migration\ValueObject\ObjectStateGroup\CreateMetadata;

final class ObjectStateGroupCreateStep implements StepInterface, ActionsAwareStepInterface
{
    use ActionsAwareStepTrait;

    /** @var \Ibexa\Migration\ValueObject\ObjectStateGroup\CreateMetadata */
    public $metadata;

    public function __construct(CreateMetadata $metadata)
    {
        $this->metadata = $metadata;
    }
}

class_alias(ObjectStateGroupCreateStep::class, 'Ibexa\Platform\Migration\ValueObject\Step\ObjectStateGroupCreateStep');
