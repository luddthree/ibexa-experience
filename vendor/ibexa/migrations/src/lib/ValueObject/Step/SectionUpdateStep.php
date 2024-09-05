<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Step;

use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Ibexa\Migration\ValueObject\Section\Matcher;
use Ibexa\Migration\ValueObject\Section\UpdateMetadata;
use Webmozart\Assert\Assert;

final class SectionUpdateStep implements StepInterface, ActionsAwareStepInterface, ReferenceAwareStepInterface
{
    use ActionsAwareStepTrait;
    use ReferenceAwareStepTrait;

    /** @var \Ibexa\Migration\ValueObject\Section\Matcher */
    public $match;

    /** @var \Ibexa\Migration\ValueObject\Section\UpdateMetadata */
    public $metadata;

    /**
     * @param \Ibexa\Migration\Generator\Reference\ReferenceDefinition[] $references
     */
    public function __construct(
        Matcher $match,
        UpdateMetadata $metadata,
        array $references = []
    ) {
        $this->metadata = $metadata;
        $this->match = $match;

        Assert::allIsInstanceOf($references, ReferenceDefinition::class);
        $this->references = $references;
    }
}

class_alias(SectionUpdateStep::class, 'Ibexa\Platform\Migration\ValueObject\Step\SectionUpdateStep');
