<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition;

final class ReactBlockDefinition extends BlockDefinition
{
    protected string $component;

    public function setComponent(string $component): void
    {
        $this->component = $component;
    }

    public function getComponent(): string
    {
        return $this->component;
    }
}
