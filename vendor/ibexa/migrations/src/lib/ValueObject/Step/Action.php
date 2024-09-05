<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Step;

interface Action
{
    /**
     * @deprecated use instance-specific getter methods instead.
     *
     * @return mixed
     */
    public function getValue();

    public function getSupportedType(): string;
}

class_alias(Action::class, 'Ibexa\Platform\Migration\ValueObject\Step\Action');
