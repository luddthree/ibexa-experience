<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Step;

use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Ibexa\Migration\ValueObject\Step\Role\CreateMetadata;
use Ibexa\Migration\ValueObject\Step\Role\Policy;
use Webmozart\Assert\Assert;

final class RoleCreateStep implements StepInterface, ActionsAwareStepInterface, ReferenceAwareStepInterface
{
    use ActionsAwareStepTrait;
    use ReferenceAwareStepTrait;

    /** @var \Ibexa\Migration\ValueObject\Step\Role\CreateMetadata */
    public $metadata;

    /** @var \Ibexa\Migration\ValueObject\Step\Role\Policy[] */
    public $policies;

    /**
     * @param \Ibexa\Migration\ValueObject\Step\Role\Policy[] $policies
     * @param \Ibexa\Migration\Generator\Reference\ReferenceDefinition[] $references
     * @param \Ibexa\Migration\ValueObject\Step\Action[] $actions
     */
    public function __construct(
        CreateMetadata $metadata,
        array $policies,
        array $references = [],
        array $actions = []
    ) {
        $this->metadata = $metadata;
        Assert::allIsInstanceOf($policies, Policy::class);
        $this->policies = $policies;
        Assert::allIsInstanceOf($actions, Action::class);
        $this->actions = $actions;
        Assert::allIsInstanceOf($references, ReferenceDefinition::class);
        $this->references = $references;
    }
}

class_alias(RoleCreateStep::class, 'Ibexa\Platform\Migration\ValueObject\Step\RoleCreateStep');
