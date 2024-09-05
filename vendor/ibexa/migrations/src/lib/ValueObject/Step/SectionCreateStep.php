<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Step;

use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Ibexa\Migration\ValueObject\Section\CreateMetadata;
use Webmozart\Assert\Assert;

final class SectionCreateStep implements StepInterface, ActionsAwareStepInterface, ReferenceAwareStepInterface
{
    use ActionsAwareStepTrait;
    use ReferenceAwareStepTrait;

    /** @var \Ibexa\Migration\ValueObject\Section\CreateMetadata */
    public $metadata;

    /**
     * @param \Ibexa\Migration\Generator\Reference\ReferenceDefinition[] $references
     */
    public function __construct(CreateMetadata $metadata, array $references = [])
    {
        $this->metadata = $metadata;

        Assert::allIsInstanceOf($references, ReferenceDefinition::class);
        $this->references = $references;
    }
}

class_alias(SectionCreateStep::class, 'Ibexa\Platform\Migration\ValueObject\Step\SectionCreateStep');
