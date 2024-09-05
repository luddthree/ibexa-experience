<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Connect\REST\Value\Template;

use Ibexa\Rest\Value;

final class Parameter extends Value
{
    /** @var non-empty-string */
    public string $name;

    /** @var non-empty-string */
    public string $label;

    /** @var non-empty-string */
    public string $type;

    public bool $required;

    /** @phpstan-var array<mixed> */
    public array $options;

    public function __construct(\Ibexa\Connect\PageBuilder\Template\Parameter $parameter)
    {
        $this->name = $parameter->getName();
        $this->label = $parameter->getLabel();
        $this->type = $parameter->getType();
        $this->required = $parameter->isRequired();
        $this->options = $parameter->getOptions();
    }
}
