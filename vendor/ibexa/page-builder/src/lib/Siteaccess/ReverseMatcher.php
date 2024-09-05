<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\Siteaccess;

use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\MVC\Symfony\SiteAccess\Matcher;
use Ibexa\Core\MVC\Symfony\SiteAccess\Matcher\CompoundInterface;
use Ibexa\Core\MVC\Symfony\SiteAccess\MatcherBuilderInterface;
use Ibexa\Core\MVC\Symfony\SiteAccess\Router;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessService as RepositorySiteAccessService;
use Ibexa\Core\MVC\Symfony\SiteAccess\VersatileMatcher;
use function in_array;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\HttpFoundation\RequestStack;

class ReverseMatcher
{
    /** @var \Ibexa\Core\MVC\Symfony\SiteAccess\Router */
    private $siteaccessRouter;

    /** @var \Symfony\Component\HttpFoundation\RequestStack */
    private $requestStack;

    /** @var \Ibexa\Core\MVC\Symfony\SiteAccess\MatcherBuilderInterface */
    private $matcherBuilder;

    /** @var \Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessService */
    private $siteAccessService;

    /**
     * @param \Ibexa\Core\MVC\Symfony\SiteAccess\Router $siteaccessRouter
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
     * @param \Ibexa\Core\MVC\Symfony\SiteAccess\MatcherBuilderInterface $matcherBuilder
     * @param \Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessService $siteAccessService
     */
    public function __construct(
        Router $siteaccessRouter,
        RequestStack $requestStack,
        MatcherBuilderInterface $matcherBuilder,
        RepositorySiteAccessService $siteAccessService
    ) {
        $this->siteaccessRouter = $siteaccessRouter;
        $this->requestStack = $requestStack;
        $this->matcherBuilder = $matcherBuilder;
        $this->siteAccessService = $siteAccessService;
    }

    /**
     * Returns the VersatileMatcher object containing request corresponding to the reverse match.
     * This request object can then be used to build a link to the "reverse matched" SiteAccess.
     *
     * @param string $siteAccessName
     *
     * @return \Ibexa\Core\MVC\Symfony\SiteAccess\VersatileMatcher
     *
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentException
     */
    public function getMatcher($siteAccessName): VersatileMatcher
    {
        if (!$this->siteAccessService->exists($siteAccessName)) {
            throw new InvalidArgumentException('SiteAccess', $siteAccessName);
        }

        $siteAccessList = $this->getSiteAccessList();
        foreach ($siteAccessList as $siteaccessName) {
            $siteaccess = $this->siteaccessRouter->matchByName($siteaccessName);
            $matcher = $siteaccess->matcher;

            if (!$matcher instanceof VersatileMatcher) {
                continue;
            }

            if ($matcher instanceof CompoundInterface) {
                $matcher->setMatcherBuilder($this->matcherBuilder);
            }

            $reverseMatcher = $matcher->reverseMatch($siteAccessName);
            if (!$reverseMatcher instanceof Matcher) {
                continue;
            }

            return $reverseMatcher;
        }

        throw new InvalidConfigurationException(
            sprintf('SiteAccess "%s" could not be reverse-matched against configuration. No VersatileMatcher found.', $siteAccessName)
        );
    }

    /**
     * @param string $siteAccessName
     *
     * @return array
     *
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentException
     */
    public function getUrlParts(string $siteAccessName): array
    {
        $matcher = $this->getMatcher($siteAccessName);
        $request = $matcher->getRequest();
        $masterRequest = $this->requestStack->getMainRequest();

        return [
            'scheme' => $request->scheme ?: $masterRequest->getScheme(),
            'host' => $request->host ?: $masterRequest->getHost(),
            'port' => $request->port ?: $masterRequest->getPort(),
            'uri' => $request->pathinfo,
        ];
    }

    /**
     * @param string $siteAccessName
     *
     * @return string
     *
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentException
     */
    public function getSchemeAndHttpHost(string $siteAccessName): string
    {
        $matcher = $this->getMatcher($siteAccessName);
        $request = $matcher->getRequest();
        $masterRequest = $this->requestStack->getMainRequest();
        $port = $request->port ?: $masterRequest->getPort();
        $scheme = $request->scheme ?: $masterRequest->getScheme();

        if (443 === $port) {
            $scheme = 'https';
        }

        $host = $request->host ?: $masterRequest->getHost();

        return sprintf(
            '%s://%s%s',
            $scheme,
            $host,
            (('http' === $scheme && 80 === $port) || ('https' === $scheme && 443 === $port)) ? '' : ":{$port}"
        );
    }

    /**
     * @return array
     */
    public function getSiteAccessDomains(): array
    {
        $domains = [];

        foreach ($this->getSiteAccessList() as $siteAccessName) {
            $parts = $this->getUrlParts($siteAccessName);

            $domains[] = sprintf(
                '%s://%s%s',
                $parts['scheme'],
                $parts['host'],
                !in_array($parts['port'], [80, 443]) ? ":{$parts['port']}" : ''
            );
        }

        return $domains;
    }

    /**
     * @param string $siteAccessName
     *
     * @return string
     */
    public function getUrlRoot(string $siteAccessName): string
    {
        $parts = $this->getUrlParts($siteAccessName);

        return sprintf(
            '%s://%s%s%s',
            $parts['scheme'],
            $parts['host'],
            !in_array($parts['port'], [80, 443]) ? ":{$parts['port']}" : '',
            $parts['uri']
        );
    }

    /**
     * @return bool
     */
    public function isMultiDomain(): bool
    {
        $hosts = [];

        foreach ($this->getSiteAccessList() as $siteAccessName) {
            $hosts[] = $this->getUrlParts($siteAccessName)['host'];
        }

        return count(array_unique($hosts)) > 1;
    }

    /**
     * @return string[]
     */
    private function getSiteAccessList(): array
    {
        $allSiteAccessList = $this->siteAccessService->getAll();

        return array_column(
            is_array($allSiteAccessList) ? $allSiteAccessList : iterator_to_array($allSiteAccessList),
            'name'
        );
    }
}

class_alias(ReverseMatcher::class, 'EzSystems\EzPlatformPageBuilder\Siteaccess\ReverseMatcher');
