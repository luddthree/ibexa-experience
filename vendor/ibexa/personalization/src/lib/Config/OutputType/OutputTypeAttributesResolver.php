<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Config\OutputType;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Personalization\SiteAccess\ScopeParameterResolver;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class OutputTypeAttributesResolver implements OutputTypeAttributesResolverInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    private const PARAMETER_NAME = 'personalization.output_type_attributes';

    private ConfigResolverInterface $configResolver;

    private ScopeParameterResolver $scopeParameterResolver;

    private SiteAccessServiceInterface $siteAccessService;

    public function __construct(
        ConfigResolverInterface $configResolver,
        ScopeParameterResolver $scopeParameterResolver,
        SiteAccessServiceInterface $siteAccessService,
        ?LoggerInterface $logger = null
    ) {
        $this->configResolver = $configResolver;
        $this->scopeParameterResolver = $scopeParameterResolver;
        $this->siteAccessService = $siteAccessService;
        $this->logger = $logger ?? new NullLogger();
    }

    public function resolve(int $customerId): array
    {
        $siteAccess = $this->getSiteAccessName($customerId);
        if (!$this->configResolver->hasParameter(self::PARAMETER_NAME, null, $siteAccess)) {
            $this->logger->warning('Output type attributes are not configured for customerId: ' . $customerId);

            return [];
        }

        return $this->configResolver->getParameter(self::PARAMETER_NAME, null, $siteAccess);
    }

    private function getSiteAccessName(int $customerId): ?string
    {
        $siteAccessList = $this->siteAccessService->getAll();
        foreach ($siteAccessList as $siteAccess) {
            if ($customerId === $this->scopeParameterResolver->getCustomerIdForScope($siteAccess)) {
                return $siteAccess->name;
            }
        }

        return null;
    }
}
