<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Step;

use Ibexa\Contracts\Core\Repository\Values\Filter\FilteringCriterion;

final class ContentDeleteStep implements ActionsAwareStepInterface
{
    use ActionsAwareStepTrait;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Filter\FilteringCriterion */
    public $criterion;

    public function __construct(FilteringCriterion $criterion)
    {
        $this->criterion = $criterion;
    }
}

class_alias(ContentDeleteStep::class, 'Ibexa\Platform\Migration\ValueObject\Step\ContentDeleteStep');
