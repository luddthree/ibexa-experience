<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Step;

use Ibexa\Migration\ValueObject\ContentTypeGroup\Matcher;
use Ibexa\Migration\ValueObject\ContentTypeGroup\UpdateMetadata;

final class ContentTypeGroupUpdateStep implements StepInterface, ActionsAwareStepInterface
{
    use ActionsAwareStepTrait;

    /** @var \Ibexa\Migration\ValueObject\ContentTypeGroup\UpdateMetadata */
    public $metadata;

    /** @var \Ibexa\Migration\ValueObject\ContentTypeGroup\Matcher */
    public $match;

    public function __construct(UpdateMetadata $metadata, Matcher $match)
    {
        $this->metadata = $metadata;
        $this->match = $match;
    }
}

class_alias(ContentTypeGroupUpdateStep::class, 'Ibexa\Platform\Migration\ValueObject\Step\ContentTypeGroupUpdateStep');
