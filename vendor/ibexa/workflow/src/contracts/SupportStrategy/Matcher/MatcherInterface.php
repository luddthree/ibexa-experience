<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Workflow\SupportStrategy\Matcher;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

interface MatcherInterface
{
    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\ValueObject $subject
     * @param array $conditions
     *
     * @return bool
     */
    public function match(ValueObject $subject, array $conditions): bool;

    /**
     * @return string
     */
    public function getIdentifier(): string;
}

class_alias(MatcherInterface::class, 'EzSystems\EzPlatformWorkflow\SupportStrategy\Matcher\MatcherInterface');
