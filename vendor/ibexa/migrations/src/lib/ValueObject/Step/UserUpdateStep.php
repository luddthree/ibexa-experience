<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Step;

use Ibexa\Contracts\Core\Repository\Values\Filter\FilteringCriterion;
use Ibexa\Migration\ValueObject\User\UpdateMetadata;

final class UserUpdateStep implements StepInterface, ActionsAwareStepInterface
{
    use ActionsAwareStepTrait;

    /** @var \Ibexa\Migration\ValueObject\User\UpdateMetadata */
    public $metadata;

    /** @var \Ibexa\Migration\ValueObject\Content\Field[] */
    public $fields;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Filter\FilteringCriterion */
    public $criterion;

    /**
     * @param \Ibexa\Migration\ValueObject\Content\Field[] $fields
     */
    public function __construct(UpdateMetadata $metadata, FilteringCriterion $criterion, array $fields)
    {
        $this->metadata = $metadata;
        $this->criterion = $criterion;
        $this->fields = $fields;
    }
}

class_alias(UserUpdateStep::class, 'Ibexa\Platform\Migration\ValueObject\Step\UserUpdateStep');
