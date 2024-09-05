<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\SiteFactory\Behat\Context;

use Behat\MinkExtension\Context\RawMinkContext;
use Ibexa\Contracts\Core\Repository\LanguageService;
use Ibexa\Contracts\SiteFactory\Service\SiteServiceInterface;
use Ibexa\Contracts\SiteFactory\Values\Site\PublicAccess;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteQuery;
use PHPUnit\Framework\Assert;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Stopwatch\Stopwatch;

class SiteFactoryFrontendContext extends RawMinkContext
{
    /** @var \Ibexa\Contracts\Core\Repository\LanguageService */
    private $languageService;

    /** @var \Ibexa\Contracts\SiteFactory\Service\SiteServiceInterface */
    private $siteService;

    /** @var \Symfony\Component\Stopwatch\Stopwatch */
    private $stopwatch;

    private const EXPECTED_NUMBER_OF_PUBLIC_ACCESSES = 20;

    private const SAMPLE_SIZE = 50;

    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    public function __construct(SiteServiceInterface $siteService, LanguageService $languageService, LoggerInterface $logger)
    {
        $this->languageService = $languageService;
        $this->siteService = $siteService;
        $this->stopwatch = new Stopwatch();
        $this->logger = $logger;
    }

    /**
     * @Then There are :expectedNumberOfSites Sites with correct data
     */
    public function verifySites(int $expectedNumberOfSites): void
    {
        $query = new SiteQuery();
        $query->limit = -1;

        $sites = $this->siteService->loadSites($query)->getSites();
        Assert::assertCount($expectedNumberOfSites, $sites);

        $sitesSample = $this->getSitesSample($sites, self::SAMPLE_SIZE);

        $this->logger->log(LogLevel::CRITICAL, 'Starting measurements');
        $this->stopwatch->start('total');

        foreach ($sitesSample as $site) {
            Assert::assertCount(self::EXPECTED_NUMBER_OF_PUBLIC_ACCESSES, $site->publicAccesses);

            foreach ($site->publicAccesses as $publicAccess) {
                $host = $publicAccess->getMatcherConfiguration()->host;
                $path = $publicAccess->getMatcherConfiguration()->path;

                $mainSiteUrl = sprintf('http://%s/%s', $host, $path);
                $this->verifyPage($publicAccess, [$this, 'getExpectedTemplateDataForMainPage'], $mainSiteUrl);

                if ($path) {
                    $childItemUrl = sprintf('http://%s/%s/Item1/child1english-united-kingdom', $host, $path);
                } else {
                    $childItemUrl = sprintf('http://%s/Item1/child1english-united-kingdom', $host);
                }

                $this->verifyPage($publicAccess, [$this, 'getExpectedTemplateDataForChildItem'], $childItemUrl);
            }
        }

        $this->stopwatch->stop('total');

        $duration = $this->stopwatch->getEvent('total')->getDuration();

        $totalPagesChecked = $expectedNumberOfSites * self::EXPECTED_NUMBER_OF_PUBLIC_ACCESSES;

        $average = $duration / $totalPagesChecked;
        $averageInSecods = $average / 1000;
        $this->logger->log(LogLevel::CRITICAL, sprintf('Total duration: %f s, with average %f s per Page', $duration / 1000, $averageInSecods));
    }

    private function verifyPage(PublicAccess $publicAccess, callable $expectedTemplateDataProvider, string $url)
    {
        $this->visitPath($url);

        $languageName = $this->languageService->loadLanguage($publicAccess->getMainLanguage())->name;

        [$expectedTemplateName, $expectedEmbeddedItemName, $expectedEmbeddedWithESIName, $expectedSiteRootLocationName] = $expectedTemplateDataProvider($publicAccess, $languageName, $url);

        Assert::assertEquals($expectedTemplateName, $this->getSession()->getPage()->findById('template')->getText());
        Assert::assertEquals($expectedEmbeddedItemName, $this->getSession()->getPage()->findById('render')->getText());
        Assert::assertEquals($expectedEmbeddedWithESIName, $this->getSession()->getPage()->findById('renderESI')->getText());
        Assert::assertEquals($expectedSiteRootLocationName, $this->getSession()->getPage()->findById('siteName')->getText());
    }

    public function getExpectedTemplateDataForMainPage(PublicAccess $publicAccess, string $languageName, string $url): array
    {
        $matches = [];
        preg_match('/test(\d+).local/', $url, $matches);
        $siteItemName = sprintf('TestSite%d', $matches[1]);

        if ($publicAccess->getMainLanguage() === 'eng-GB') {
            return [
                sprintf('SiteFactory_%s', $publicAccess->getDesign()),
                sprintf('Item: %s', $siteItemName),
                sprintf('Item with ESI: %s', $siteItemName),
                sprintf('Site: %s', $siteItemName),
            ];
        }

        return [
            sprintf('SiteFactory_%s', $publicAccess->getDesign()),
            sprintf('Item: Skeleton%s', $languageName),
            sprintf('Item with ESI: Skeleton%s', $languageName),
            sprintf('Site: Skeleton%s', $languageName),
        ];
    }

    public function getExpectedTemplateDataForChildItem(PublicAccess $publicAccess, string $languageName, string $url): array
    {
        $matches = [];
        preg_match('/test(\d+).local/', $url, $matches);
        $siteItemName = sprintf('TestSite%d', $matches[1]);

        if ($publicAccess->getMainLanguage() === 'eng-GB') {
            return [
                sprintf('SiteFactory_%s', $publicAccess->getDesign()),
                sprintf('Item: Child1%s', $languageName),
                sprintf('Item with ESI: Child1%s', $languageName),
                sprintf('Site: %s', $siteItemName),
            ];
        }

        return [
            sprintf('SiteFactory_%s', $publicAccess->getDesign()),
            sprintf('Item: Child1%s', $languageName),
            sprintf('Item with ESI: Child1%s', $languageName),
            sprintf('Site: Skeleton%s', $languageName),
        ];
    }

    /**
     * @param \Ibexa\Contracts\SiteFactory\Values\Site\Site[] $sites
     *
     * @return \Ibexa\Contracts\SiteFactory\Values\Site\Site[]
     */
    private function getSitesSample(array $sites, int $sampleSize): array
    {
        if ($sampleSize > count($sites)) {
            return $sites;
        }

        $randomSample = [];

        $indices = array_rand($sites, $sampleSize);
        foreach ($indices as $index) {
            $randomSample[] = $sites[$index];
        }

        return $randomSample;
    }
}

class_alias(SiteFactoryFrontendContext::class, 'EzSystems\EzPlatformSiteFactory\Behat\Context\SiteFactoryFrontendContext');
