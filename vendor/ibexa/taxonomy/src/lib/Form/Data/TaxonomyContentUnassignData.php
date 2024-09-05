<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Form\Data;

use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Symfony\Component\Validator\Constraints as Assert;

class TaxonomyContentUnassignData
{
    private ?TaxonomyEntry $entry;

    /**
     * @Assert\NotBlank()
     *
     * @var array<int, bool>
     */
    private array $assignedContentItems;

    /**
     * @param array<int, bool> $assignedContentItems
     */
    public function __construct(
        ?TaxonomyEntry $entry = null,
        array $assignedContentItems = []
    ) {
        $this->entry = $entry;
        $this->assignedContentItems = $assignedContentItems;
    }

    public function getEntry(): ?TaxonomyEntry
    {
        return $this->entry;
    }

    public function setEntry(?TaxonomyEntry $entry): void
    {
        $this->entry = $entry;
    }

    /**
     * @return array<int, bool>
     */
    public function getAssignedContentItems(): array
    {
        return $this->assignedContentItems;
    }

    /**
     * @param array<int, bool> $assignedContentItems
     */
    public function setAssignedContentItems(array $assignedContentItems): void
    {
        $this->assignedContentItems = $assignedContentItems;
    }
}
