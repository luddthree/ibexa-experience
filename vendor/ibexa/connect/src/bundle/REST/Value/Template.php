<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Connect\REST\Value;

use Ibexa\Bundle\Connect\REST\Value\Template\Parameter;
use Ibexa\Rest\Value;

final class Template extends Value
{
    /** @var non-empty-string */
    public string $id;

    /** @var non-empty-string */
    public string $label;

    /** @var non-empty-string */
    public string $template;

    /** @var \Ibexa\Bundle\Connect\REST\Value\Template\Parameter[] */
    public array $parameters = [];

    public function __construct(\Ibexa\Connect\PageBuilder\Template $template)
    {
        $this->id = $template->getId();
        $this->label = $template->getLabel();
        $this->template = $template->getTemplate();
        foreach ($template->getParameters() as $parameter) {
            $this->parameters[] = new Parameter($parameter);
        }
    }
}
