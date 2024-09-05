<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Step;

use Ibexa\Migration\ValueObject\ObjectState\CreateMetadata;

final class ObjectStateCreateStep implements StepInterface, ActionsAwareStepInterface
{
    use ActionsAwareStepTrait;

    /** @var \Ibexa\Migration\ValueObject\ObjectState\CreateMetadata */
    public $metadata;

    public function __construct(CreateMetadata $metadata)
    {
        $this->metadata = $metadata;
    }
}

class_alias(ObjectStateCreateStep::class, 'Ibexa\Platform\Migration\ValueObject\Step\ObjectStateCreateStep');
