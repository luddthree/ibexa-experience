<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Step;

use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Ibexa\Migration\ValueObject\Content\CreateMetadata;
use Ibexa\Migration\ValueObject\Content\Field;
use Ibexa\Migration\ValueObject\Content\Location;
use Webmozart\Assert\Assert;

final class ContentCreateStep implements ActionsAwareStepInterface, ReferenceAwareStepInterface, UserContextAwareStepInterface
{
    use ActionsAwareStepTrait;
    use ReferenceAwareStepTrait;

    /** @var \Ibexa\Migration\ValueObject\Content\CreateMetadata */
    public $metadata;

    /** @var \Ibexa\Migration\ValueObject\Content\Location */
    public $location;

    /** @var \Ibexa\Migration\ValueObject\Content\Field[] */
    public $fields;

    /**
     * @param \Ibexa\Migration\ValueObject\Content\Field[] $fields
     * @param \Ibexa\Migration\Generator\Reference\ReferenceDefinition[] $references
     * @param \Ibexa\Migration\ValueObject\Step\Action[] $actions
     */
    public function __construct(
        CreateMetadata $metadata,
        Location $location,
        array $fields,
        array $references = [],
        ?array $actions = []
    ) {
        Assert::allIsInstanceOf($actions, Action::class);
        Assert::allIsInstanceOf($references, ReferenceDefinition::class);
        Assert::allIsInstanceOf($fields, Field::class);

        $this->metadata = $metadata;
        $this->location = $location;
        $this->fields = $fields;
        $this->references = $references;
        $this->actions = $actions ?? [];
    }

    public function getUserId(): ?int
    {
        return $this->metadata->creatorId;
    }
}

class_alias(ContentCreateStep::class, 'Ibexa\Platform\Migration\ValueObject\Step\ContentCreateStep');
