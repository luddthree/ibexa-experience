<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Seo\Resolver;

/**
 * @internal
 */
interface SeoTypesResolverInterface
{
    /**
     * @return array<string, array{
     *     label: string,
     *     template: string,
     *     fields: array<string, array{
     *         label: string,
     *         type: string,
     *         key: string|null,
     *     }>,
     * }>
     */
    public function getFieldsByTypes(): array;
}
