<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Connect\PageBuilder;

final class TemplateRegistry
{
    /** @var iterable<\Ibexa\Connect\PageBuilder\Template> */
    private iterable $templates;

    /**
     * @phpstan-param iterable<\Ibexa\Connect\PageBuilder\Template> $templates
     */
    public function __construct(iterable $templates)
    {
        $this->templates = $templates;
    }

    /**
     * @return iterable<\Ibexa\Connect\PageBuilder\Template>
     */
    public function getTemplates(): iterable
    {
        return $this->templates;
    }
}
