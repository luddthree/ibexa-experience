<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Step;

use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Ibexa\Migration\ValueObject\Language\Metadata;
use Webmozart\Assert\Assert;

final class LanguageCreateStep implements ActionsAwareStepInterface, ReferenceAwareStepInterface
{
    use ActionsAwareStepTrait;
    use ReferenceAwareStepTrait;

    /** @var \Ibexa\Migration\ValueObject\Language\Metadata */
    public $metadata;

    /**
     * @param \Ibexa\Migration\Generator\Reference\ReferenceDefinition[] $references
     * @param \Ibexa\Migration\ValueObject\Step\Action[] $actions
     */
    public function __construct(
        Metadata $metadata,
        array $references = [],
        ?array $actions = []
    ) {
        $this->metadata = $metadata;

        Assert::allIsInstanceOf($references, ReferenceDefinition::class);
        Assert::allIsInstanceOf($actions, Action::class);

        $this->references = $references;
        $this->actions = $actions;
    }
}

class_alias(LanguageCreateStep::class, 'Ibexa\Platform\Migration\ValueObject\Step\LanguageCreateStep');
