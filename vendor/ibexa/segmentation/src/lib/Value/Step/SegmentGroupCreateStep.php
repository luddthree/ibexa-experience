<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\Value\Step;

use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Ibexa\Migration\ValueObject\Step\ReferenceAwareStepInterface;
use Ibexa\Migration\ValueObject\Step\ReferenceAwareStepTrait;
use Webmozart\Assert\Assert;

final class SegmentGroupCreateStep implements ReferenceAwareStepInterface
{
    use ReferenceAwareStepTrait;

    /** @var string */
    private $name;

    /** @var string */
    private $identifier;

    /**
     * @param \Ibexa\Migration\Generator\Reference\ReferenceDefinition[] $references
     */
    public function __construct(
        string $identifier,
        string $name,
        array $references = []
    ) {
        Assert::allIsInstanceOf($references, ReferenceDefinition::class);

        $this->references = $references;
        $this->identifier = $identifier;
        $this->name = $name;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
