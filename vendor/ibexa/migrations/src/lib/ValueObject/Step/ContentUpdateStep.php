<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Step;

use Ibexa\Contracts\Core\Repository\Values\Filter\FilteringCriterion;
use Ibexa\Migration\ValueObject\Content\Field;
use Ibexa\Migration\ValueObject\Content\UpdateMetadata;
use Webmozart\Assert\Assert;

final class ContentUpdateStep implements ActionsAwareStepInterface
{
    use ActionsAwareStepTrait;

    /** @var \Ibexa\Migration\ValueObject\Content\UpdateMetadata */
    public $metadata;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Filter\FilteringCriterion */
    public $criterion;

    /** @var iterable<\Ibexa\Migration\ValueObject\Content\Field> */
    public $fields;

    /**
     * @param \Ibexa\Migration\ValueObject\Content\Field[] $fields
     * @param \Ibexa\Migration\ValueObject\Step\Action[] $actions
     */
    public function __construct(
        UpdateMetadata $metadata,
        FilteringCriterion $criterion,
        iterable $fields,
        ?array $actions = []
    ) {
        Assert::allIsInstanceOf($fields, Field::class);
        Assert::allIsInstanceOf($actions, Action::class);

        $this->metadata = $metadata;
        $this->criterion = $criterion;
        $this->fields = $fields;
        $this->actions = $actions;
    }
}

class_alias(ContentUpdateStep::class, 'Ibexa\Platform\Migration\ValueObject\Step\ContentUpdateStep');
