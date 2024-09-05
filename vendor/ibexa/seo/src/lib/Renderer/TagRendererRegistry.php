<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Seo\Renderer;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Seo\Renderer\TagRendererRegistryInterface;
use Ibexa\Seo\Exception\InvalidTypeAndFieldException;

final class TagRendererRegistry implements TagRendererRegistryInterface
{
    /** @var iterable<\Ibexa\Contracts\Seo\Renderer\Tag\TagRendererInterface> */
    private $renderers;

    /**
     * @param iterable<\Ibexa\Contracts\Seo\Renderer\Tag\TagRendererInterface> $renderers
     */
    public function __construct(iterable $renderers)
    {
        $this->renderers = $renderers;
    }

    public function render(
        Content $content,
        string $type,
        string $field,
        string $value,
        array $fieldConfig
    ): string {
        foreach ($this->renderers as $renderer) {
            if ($renderer->support($type, $field)) {
                return $renderer->render($content, $type, $field, $value, $fieldConfig);
            }
        }

        throw new InvalidTypeAndFieldException($type, $field);
    }
}
