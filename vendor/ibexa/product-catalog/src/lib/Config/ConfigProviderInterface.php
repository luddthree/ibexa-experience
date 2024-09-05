<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Config;

interface ConfigProviderInterface
{
    /**
     * Return currently used engine alias.
     */
    public function getEngineAlias(): ?string;

    /**
     * Returns current engine type e.g. 'local', 'pim'.
     *
     * @throws \Ibexa\Contracts\ProductCatalog\Exceptions\ConfigurationException
     */
    public function getEngineType(): string;

    /**
     * Returns current engine option name.
     *
     * @param mixed|null $default
     *
     * @return mixed
     *
     * @throws \Ibexa\Contracts\ProductCatalog\Exceptions\ConfigurationException
     */
    public function getEngineOption(string $name, $default = null);
}
