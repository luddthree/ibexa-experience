<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Step;

use Ibexa\Migration\ValueObject\Location\Matcher;
use Ibexa\Migration\ValueObject\Location\UpdateMetadata;

final class LocationUpdateStep implements StepInterface, ActionsAwareStepInterface
{
    use ActionsAwareStepTrait;

    /** @var \Ibexa\Migration\ValueObject\Location\UpdateMetadata */
    public $metadata;

    /** @var \Ibexa\Migration\ValueObject\Location\Matcher */
    public $match;

    public function __construct(UpdateMetadata $metadata, Matcher $match)
    {
        $this->metadata = $metadata;
        $this->match = $match;
    }
}

class_alias(LocationUpdateStep::class, 'Ibexa\Platform\Migration\ValueObject\Step\LocationUpdateStep');
