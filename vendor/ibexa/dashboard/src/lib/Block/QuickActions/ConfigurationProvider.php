<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Dashboard\Block\QuickActions;

use Ibexa\Contracts\Core\Exception\InvalidArgumentException;

/**
 * @internal
 */
final class ConfigurationProvider implements ConfigurationProviderInterface
{
    /** @var iterable<\Ibexa\Contracts\Dashboard\Block\QuickActions\ActionInterface> */
    private iterable $configuration;

    /**
     * @param iterable<\Ibexa\Contracts\Dashboard\Block\QuickActions\ActionInterface> $configuration
     */
    public function __construct(iterable $configuration)
    {
        $this->configuration = $configuration;
    }

    public function getConfiguration(string $actionIdentifier): array
    {
        foreach ($this->configuration as $actionConfiguration) {
            if ($actionIdentifier === $actionConfiguration::getIdentifier()) {
                return $actionConfiguration->getConfiguration();
            }
        }

        throw new InvalidArgumentException('$actionIdentifier', "Unknown quick action '$actionIdentifier'");
    }
}
