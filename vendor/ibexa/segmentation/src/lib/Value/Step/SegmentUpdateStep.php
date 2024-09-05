<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\Value\Step;

use Ibexa\Migration\ValueObject\Step\Action;
use Ibexa\Migration\ValueObject\Step\ActionsAwareStepInterface;
use Ibexa\Migration\ValueObject\Step\ActionsAwareStepTrait;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Segmentation\Value\SegmentMatcher;
use Webmozart\Assert\Assert;

final class SegmentUpdateStep implements StepInterface, ActionsAwareStepInterface
{
    use ActionsAwareStepTrait;

    /** @var \Ibexa\Segmentation\Value\SegmentMatcher */
    private $matcher;

    /** @var string|null */
    private $name;

    /** @var string|null */
    private $identifier;

    /**
     * @param \Ibexa\Migration\ValueObject\Step\Action[] $actions
     */
    public function __construct(
        SegmentMatcher $matcher,
        ?string $identifier = null,
        ?string $name = null,
        array $actions = []
    ) {
        Assert::allIsInstanceOf($actions, Action::class);

        $this->matcher = $matcher;
        $this->actions = $actions;
        $this->identifier = $identifier;
        $this->name = $name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function getMatcher(): SegmentMatcher
    {
        return $this->matcher;
    }
}
