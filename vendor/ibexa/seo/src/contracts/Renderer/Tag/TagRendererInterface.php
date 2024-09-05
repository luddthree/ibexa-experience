<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Seo\Renderer\Tag;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;

interface TagRendererInterface
{
    public function support(string $type, string $field): bool;

    /**
     * @phpstan-param array{
     *   label: string,
     *   type: string,
     *   key: string|null,
     * } $fieldConfig
     */
    public function render(
        Content $content,
        string $type,
        string $field,
        string $value,
        array $fieldConfig
    ): string;
}
