<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Seo\Resolver;

use Ibexa\Contracts\Seo\ConfigResolver\SeoTypesInterface;
use Ibexa\Contracts\Seo\Resolver\SeoTypesResolverInterface;

/**
 * @internal
 */
final class SeoTypesResolver implements SeoTypesResolverInterface
{
    /**
     * @var array<string, array{
     *     label: string,
     *     template: string,
     *     fields: array<string, array{
     *         label: string,
     *         type: string,
     *         key: string|null,
     *     }>,
     * }>
     */
    private array $types;

    private SeoTypesInterface $seoTypesConfig;

    /**
     * @param array<string, array{
     *     label: string,
     *     template: string,
     *     fields: array<string, array{
     *         label: string,
     *         type: string,
     *         key: string|null,
     *     }>,
     * }> $types
     */
    public function __construct(
        SeoTypesInterface $seoTypesConfig,
        array $types
    ) {
        $this->seoTypesConfig = $seoTypesConfig;
        $this->types = $types;
    }

    public function getFieldsByTypes(): array
    {
        $enabledTypes = $this->seoTypesConfig->getEnabledTypes();

        return array_filter(
            $this->types,
            static fn ($item, $key): bool => in_array($key, $enabledTypes, true),
            ARRAY_FILTER_USE_BOTH
        );
    }
}
