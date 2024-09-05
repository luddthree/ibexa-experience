<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Connect\REST\Value;

use Ibexa\Rest\Value;

final class TemplateList extends Value
{
    /** @var \Ibexa\Bundle\Connect\REST\Value\Template[] */
    public array $templates;

    /**
     * @param \Ibexa\Connect\PageBuilder\Template[] $templates
     */
    public function __construct(iterable $templates)
    {
        foreach ($templates as $template) {
            $this->templates[] = new Template($template);
        }
    }
}
