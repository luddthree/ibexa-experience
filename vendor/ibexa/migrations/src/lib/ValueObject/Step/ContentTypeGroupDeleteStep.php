<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Step;

use Ibexa\Migration\ValueObject\ContentTypeGroup\Matcher;

final class ContentTypeGroupDeleteStep implements StepInterface, ActionsAwareStepInterface
{
    use ActionsAwareStepTrait;

    /** @var \Ibexa\Migration\ValueObject\ContentTypeGroup\Matcher */
    public $match;

    public function __construct(Matcher $match)
    {
        $this->match = $match;
    }
}

class_alias(ContentTypeGroupDeleteStep::class, 'Ibexa\Platform\Migration\ValueObject\Step\ContentTypeGroupDeleteStep');
