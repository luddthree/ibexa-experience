<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Step;

use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Ibexa\Migration\ValueObject\Step\Role\Matcher;
use Ibexa\Migration\ValueObject\Step\Role\PolicyList;
use Ibexa\Migration\ValueObject\Step\Role\UpdateMetadata;
use Webmozart\Assert\Assert;

final class RoleUpdateStep implements StepInterface, ActionsAwareStepInterface, ReferenceAwareStepInterface
{
    use ActionsAwareStepTrait;
    use ReferenceAwareStepTrait;

    /** @var \Ibexa\Migration\ValueObject\Step\Role\Matcher */
    public $match;

    /** @var \Ibexa\Migration\ValueObject\Step\Role\UpdateMetadata */
    public $metadata;

    /** @var \Ibexa\Migration\ValueObject\Step\Role\PolicyList|null */
    private $policyList;

    /**
     * @param \Ibexa\Migration\Generator\Reference\ReferenceDefinition[] $references
     * @param \Ibexa\Migration\ValueObject\Step\Action[] $actions
     */
    public function __construct(
        Matcher $match,
        UpdateMetadata $metadata,
        ?PolicyList $policyList,
        array $references = [],
        array $actions = []
    ) {
        $this->match = $match;
        $this->metadata = $metadata;
        $this->policyList = $policyList;

        Assert::allIsInstanceOf($actions, Action::class);
        $this->actions = $actions;
        Assert::allIsInstanceOf($references, ReferenceDefinition::class);
        $this->references = $references;
    }

    public function getPolicyList(): ?PolicyList
    {
        return $this->policyList;
    }
}

class_alias(RoleUpdateStep::class, 'Ibexa\Platform\Migration\ValueObject\Step\RoleUpdateStep');
