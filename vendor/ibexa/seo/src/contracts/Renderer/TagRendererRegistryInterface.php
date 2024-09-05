<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Seo\Renderer;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;

interface TagRendererRegistryInterface
{
    /**
     * @phpstan-param array{
     *   label: string,
     *   type: string,
     *   key: string|null,
     * } $fieldConfig
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function render(
        Content $content,
        string $type,
        string $field,
        string $value,
        array $fieldConfig
    ): string;
}
