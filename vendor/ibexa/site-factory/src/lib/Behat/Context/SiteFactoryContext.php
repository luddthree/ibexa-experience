<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\SiteFactory\Behat\Context;

use Behat\Behat\Context\Context;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\SiteFactory\Service\SiteServiceInterface;
use Ibexa\Contracts\SiteFactory\Values\Design\TemplateConfiguration;
use Ibexa\Contracts\SiteFactory\Values\Site\PublicAccess;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteAccessMatcherConfiguration;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteConfiguration;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteCreateStruct;
use Ibexa\SiteFactory\Provider\SiteFactoryConfigurationProvider;

class SiteFactoryContext implements Context
{
    /** @var \Ibexa\Contracts\SiteFactory\Service\SiteServiceInterface */
    private $siteService;

    /** @var \Ibexa\SiteFactory\Provider\SiteFactoryConfigurationProvider */
    private $siteFactoryConfigurationProvider;

    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface */
    private $configResolver;

    private const ROOT_LOCATION_ID = 2;

    public function __construct(
        SiteServiceInterface $siteService,
        SiteFactoryConfigurationProvider $siteFactoryConfigurationProvider,
        ConfigResolverInterface $configResolver
    ) {
        $this->siteService = $siteService;
        $this->siteFactoryConfigurationProvider = $siteFactoryConfigurationProvider;
        $this->configResolver = $configResolver;
    }

    /**
     * @Given I set up :numberOfSites sites
     */
    public function setUpSites(int $numberOfSites): void
    {
        /** @var \Ibexa\Contracts\SiteFactory\Values\Design\TemplateConfiguration[] $templates */
        $templates = iterator_to_array($this->siteFactoryConfigurationProvider->getTemplatesConfiguration());
        $languages = $this->configResolver->getParameter('languages', 'ibexa.site_access.config', 'admin_group');

        $hostnames = [];

        for ($i = 0; $i < $numberOfSites; ++$i) {
            $hostname = 'test' . $i . '.local';
            $template = $this->getRandomTemplate($templates);
            $this->siteService->createSite(
                $this->createSiteCreateStruct(
                    'TestSite' . $i,
                    $hostname,
                    $template,
                    $languages
                ),
            );

            $hostnames[] = $hostname;
        }

        echo sprintf('Please add the following hostnames: %s', implode(',', $hostnames));
    }

    private function createSiteCreateStruct(string $siteName, string $host, TemplateConfiguration $templateConfiguration, array $languages): SiteCreateStruct
    {
        return new SiteCreateStruct(
            $siteName,
            true,
            $this->getPublicAccesses($host, $templateConfiguration, $languages),
            self::ROOT_LOCATION_ID,
            [],
            new \DateTimeImmutable(),
            $templateConfiguration->getSiteSkeletonLocation()->id ?? null
        );
    }

    private function getRandomTemplate(array $templates): TemplateConfiguration
    {
        return $templates[array_rand($templates)];
    }

    /**
     * @return \Ibexa\Contracts\SiteFactory\Values\Site\PublicAccess[]
     */
    private function getPublicAccesses(string $host, TemplateConfiguration $templateConfiguration, array $languages): array
    {
        $publicAccesses = [
            new PublicAccess(
                null,
                null,
                $templateConfiguration->getSiteAccessGroup(),
                new SiteAccessMatcherConfiguration(
                    $host
                ),
                new SiteConfiguration(
                    [
                        SiteConfiguration::DESIGN => $templateConfiguration->getDesign(),
                        SiteConfiguration::LANGUAGES => ['eng-GB'],
                    ]
                ),
            ),
        ];

        foreach ($languages as $language) {
            $path = explode('-', $language)[0];

            $publicAccesses[] = new PublicAccess(
                null,
                null,
                $templateConfiguration->getSiteAccessGroup(),
                new SiteAccessMatcherConfiguration(
                    $host,
                    $path
                ),
                new SiteConfiguration(
                    [
                        SiteConfiguration::DESIGN => $templateConfiguration->getDesign(),
                        SiteConfiguration::LANGUAGES => array_unique([$language, 'eng-GB']),
                    ]
                )
            );
        }

        return $publicAccesses;
    }
}

class_alias(SiteFactoryContext::class, 'EzSystems\EzPlatformSiteFactory\Behat\Context\SiteFactoryContext');
