<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\View\Matcher\LocationBased;

use Ibexa\Contracts\ProductCatalog\ViewMatcher\LocationBased\RootLocation as RootLocationInterface;
use Ibexa\Core\MVC\Symfony\View\LocationValueView;
use Ibexa\Core\MVC\Symfony\View\View;
use Ibexa\ProductCatalog\Config\ConfigProviderInterface;

final class RootLocation implements RootLocationInterface
{
    private ConfigProviderInterface $configProvider;

    private bool $value = true;

    public function __construct(ConfigProviderInterface $configProvider)
    {
        $this->configProvider = $configProvider;
    }

    public function match(View $view): bool
    {
        if (!($view instanceof LocationValueView)) {
            return false;
        }

        $isRootLocation = $view->getLocation()->remoteId === $this->getRootLocationRemoteId();

        return ($this->value && $isRootLocation) || (!$this->value && !$isRootLocation);
    }

    public function setMatchingConfig($matchingConfig): void
    {
        $this->value = (bool)$matchingConfig;
    }

    private function getRootLocationRemoteId(): ?string
    {
        return $this->configProvider->getEngineOption('root_location_remote_id');
    }
}
