<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\Event\Subscriber;

use Ibexa\AdminUi\Specification\SiteAccess\IsAdmin;
use Ibexa\Contracts\PageBuilder\PageBuilder\ConfigurationResolverInterface;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\PageBuilder\Security\EditorialMode\PostAuthenticationGuardToken;
use Ibexa\PageBuilder\Siteaccess\ReverseMatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Environment;

class InjectCrossOriginHelperSubscriber implements EventSubscriberInterface
{
    /** @var \Ibexa\Contracts\PageBuilder\PageBuilder\ConfigurationResolverInterface */
    private $pageBuilderConfigResolver;

    /** @var \Twig\Environment */
    private $templating;

    /** @var array */
    private $siteaccessGroups;

    /** @var \Ibexa\PageBuilder\Siteaccess\ReverseMatcher */
    private $reverseMatcher;

    /** @var \Ibexa\Core\MVC\Symfony\SiteAccess */
    private $siteaccess;

    /** @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface */
    private $tokenStorage;

    public function __construct(
        ConfigurationResolverInterface $pageBuilderConfigResolver,
        Environment $templating,
        array $siteaccessGroups,
        ReverseMatcher $reverseMatcher,
        SiteAccess $siteaccess,
        TokenStorageInterface $tokenStorage
    ) {
        $this->pageBuilderConfigResolver = $pageBuilderConfigResolver;
        $this->templating = $templating;
        $this->siteaccessGroups = $siteaccessGroups;
        $this->reverseMatcher = $reverseMatcher;
        $this->siteaccess = $siteaccess;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => 'onResponse',
        ];
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\ResponseEvent $event
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function onResponse(ResponseEvent $event)
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $response = $event->getResponse();

        $isHelperEnabled = $this->pageBuilderConfigResolver->get('inject_cross_origin_helper', $this->siteaccess->name);
        if (null === $this->siteaccess || !$isHelperEnabled) {
            return;
        }

        $isHtmlRequest = 'html' === $request->getRequestFormat();
        $isAttachment = false !== stripos($response->headers->get('Content-Disposition', ''), 'attachment');
        $isHtmlResponse = false !== strpos($response->headers->get('Content-Type', ''), 'html');

        if (
            !$isHtmlRequest
            || !$isHtmlResponse
            || $isAttachment
            || $response->isRedirection()
            || $request->isXmlHttpRequest()
            || !$response->getContent()
            || !$this->isPreAuthenticatedFromPageBuilder()
        ) {
            return;
        }

        $isAdminSiteaccess = (new IsAdmin($this->siteaccessGroups))->isSatisfiedBy($this->siteaccess);
        if ($isAdminSiteaccess) {
            return;
        }

        if (true === $this->injectSnippetForSiteaccessHosts($request, $response)) {
            return;
        }

        $hosts = $this->getCompatibleHosts($this->siteaccess->name);
        $host = reset($hosts);

        $isAdminSiteaccessOnSameHost = 1 === \count($hosts) && $host === $request->getSchemeAndHttpHost();
        if ($isAdminSiteaccessOnSameHost) {
            return;
        }

        $this->injectSnippet($response, $hosts);
    }

    private function isPreAuthenticatedFromPageBuilder(): bool
    {
        $token = $this->tokenStorage->getToken();

        return $token instanceof PostAuthenticationGuardToken;
    }

    /**
     * @param string $requestSiteaccessName
     *
     * @return string[]
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    private function getCompatibleHosts(string $requestSiteaccessName): array
    {
        $pageBuilderCompatibleAdminSiteaccesses = $this->pageBuilderConfigResolver->reverseAdminSiteaccessMatch(
            $requestSiteaccessName
        );

        $hosts = [];
        foreach ($pageBuilderCompatibleAdminSiteaccesses as $siteaccessName) {
            $hosts[] = $this->reverseMatcher->getSchemeAndHttpHost($siteaccessName);
        }

        return array_unique($hosts);
    }

    private function injectSnippetForSiteaccessHosts(
        Request $request,
        Response $response
    ): bool {
        $siteAccessHosts = $this->pageBuilderConfigResolver->getSiteaccessHosts(
            $this->siteaccess->name
        );

        if (empty($siteAccessHosts)) {
            return false;
        }

        $scheme = $request->getScheme();
        $port = $request->getPort();
        $portPart = in_array($port, [80, 443]) ? '' : ":{$port}";
        $siteAccessUrls = [];

        foreach ($siteAccessHosts as $host) {
            $siteAccessUrls[] = $scheme . '://' . $host . $portPart;
        }

        $this->injectSnippet($response, $siteAccessUrls);

        return true;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @param string[] $hosts
     */
    private function injectSnippet(Response $response, array $hosts): void
    {
        $content = $response->getContent();
        $headTagPosition = stripos($content, '</head>');

        if (!$headTagPosition) {
            return;
        }

        $snippet = $this->templating->render(
            '@IbexaPageBuilder/cross_origin_helper/snippet.html.twig',
            ['hosts' => $hosts]
        );

        $compressedSnippet = "\n" . str_replace("\n", '', $snippet) . "\n";

        $content = substr($content, 0, $headTagPosition) .
            $compressedSnippet .
            substr($content, $headTagPosition);

        $response->setContent($content);
    }
}

class_alias(InjectCrossOriginHelperSubscriber::class, 'EzSystems\EzPlatformPageBuilder\Event\Subscriber\InjectCrossOriginHelperSubscriber');
