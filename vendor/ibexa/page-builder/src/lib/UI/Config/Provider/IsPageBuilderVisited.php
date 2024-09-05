<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\UI\Config\Provider;

use Ibexa\Contracts\AdminUi\UI\Config\ProviderInterface;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\UserPreferenceService;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class IsPageBuilderVisited implements ProviderInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    private UserPreferenceService $userPreferenceService;

    public function __construct(
        UserPreferenceService $userPreferenceService,
        ?LoggerInterface $logger = null
    ) {
        $this->userPreferenceService = $userPreferenceService;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function getConfig(): bool
    {
        try {
            return (bool)$this->userPreferenceService
                ->getUserPreference('page_builder_visited')
                ->value;
        } catch (NotFoundException $e) {
            $this->logger->info($e->getMessage(), [
                'exception' => $e,
            ]);
        }

        return false;
    }
}
