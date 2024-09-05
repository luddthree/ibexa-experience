<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Step;

use Ibexa\Migration\ValueObject\ContentType\Matcher;

final class ContentTypeDeleteStep implements StepInterface, ActionsAwareStepInterface
{
    use ActionsAwareStepTrait;

    /** @var \Ibexa\Migration\ValueObject\ContentType\Matcher */
    private $match;

    public function __construct(Matcher $match)
    {
        $this->match = $match;
    }

    public function getMatch(): Matcher
    {
        return $this->match;
    }
}

class_alias(ContentTypeDeleteStep::class, 'Ibexa\Platform\Migration\ValueObject\Step\ContentTypeDeleteStep');
