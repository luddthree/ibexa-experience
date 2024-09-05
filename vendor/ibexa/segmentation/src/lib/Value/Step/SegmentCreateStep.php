<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\Value\Step;

use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Ibexa\Migration\ValueObject\Step\Action;
use Ibexa\Migration\ValueObject\Step\ActionsAwareStepInterface;
use Ibexa\Migration\ValueObject\Step\ActionsAwareStepTrait;
use Ibexa\Migration\ValueObject\Step\ReferenceAwareStepInterface;
use Ibexa\Migration\ValueObject\Step\ReferenceAwareStepTrait;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Segmentation\Value\SegmentGroupMatcher;
use Webmozart\Assert\Assert;

final class SegmentCreateStep implements StepInterface, ActionsAwareStepInterface, ReferenceAwareStepInterface
{
    use ActionsAwareStepTrait;
    use ReferenceAwareStepTrait;

    /** @var string */
    private $name;

    /** @var string */
    private $identifier;

    /** @var \Ibexa\Segmentation\Value\SegmentGroupMatcher */
    private $group;

    /**
     * @param \Ibexa\Migration\Generator\Reference\ReferenceDefinition[] $references
     * @param \Ibexa\Migration\ValueObject\Step\Action[] $actions
     */
    public function __construct(
        string $identifier,
        string $name,
        SegmentGroupMatcher $group,
        array $references = [],
        array $actions = []
    ) {
        Assert::allIsInstanceOf($references, ReferenceDefinition::class);
        Assert::allIsInstanceOf($actions, Action::class);

        $this->references = $references;
        $this->actions = $actions;
        $this->identifier = $identifier;
        $this->name = $name;
        $this->group = $group;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getGroup(): SegmentGroupMatcher
    {
        return $this->group;
    }
}
