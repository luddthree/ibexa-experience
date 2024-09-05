<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\SiteFactory;

use Ibexa\Bundle\Core\SiteAccess\Matcher;
use Ibexa\Core\MVC\Symfony\Routing\SimplifiedRequest;
use Ibexa\Core\MVC\Symfony\SiteAccess\Matcher\Map\URI;
use Ibexa\SiteFactory\Persistence\PublicAccess\Handler\HandlerInterface;
use Ibexa\SiteFactory\Request\PathinfoExtractor;
use JsonSerializable;

final class SiteAccessMatcher extends URI implements Matcher, JsonSerializable
{
    /** @var \Ibexa\SiteFactory\Persistence\PublicAccess\Handler\HandlerInterface */
    private $handler;

    public function __construct(
        HandlerInterface $handler
    ) {
        $this->handler = $handler;
    }

    public function match()
    {
        if (!is_string($this->request->host) || !is_string($this->request->pathinfo)) {
            return false;
        }
        $result = $this->handler->match($this->request->host);

        $path = PathinfoExtractor::getFirstPathElement($this->request->pathinfo);
        // try to match the first path element
        foreach ($result as $key => $publicAccess) {
            if ($path === $publicAccess['site_matcher_path']) {
                return $publicAccess['public_access_identifier'];
            }
        }
        // check if exist public access with an empty path
        foreach ($result as $key => $publicAccess) {
            if ($publicAccess['site_matcher_path'] === '' || $publicAccess['site_matcher_path'] === null) {
                return $publicAccess['public_access_identifier'];
            }
        }

        return false;
    }

    public function getName(): string
    {
        return 'site_factory';
    }

    public function setMatchingConfiguration($matchingConfiguration)
    {
    }

    public function __sleep(): array
    {
        return [];
    }

    public function jsonSerialize(): array
    {
        return ['request' => $this->request];
    }

    /**
     * Injects the request object to match against.
     *
     * @param \Ibexa\Core\MVC\Symfony\Routing\SimplifiedRequest $request
     */
    public function setRequest(SimplifiedRequest $request)
    {
        if (is_string($request->host) && is_string($request->pathinfo)) {
            $result = $this->handler->match($request->host);

            $path = PathinfoExtractor::getFirstPathElement($request->pathinfo);
            // try to match the first path element
            foreach ($result as $key => $publicAccess) {
                if ($path === $publicAccess['site_matcher_path']) {
                    $this->setMapKey($publicAccess['site_matcher_path']);
                }
            }
        }

        $this->request = $request;
    }

    /**
     * Fixes up $uri to remove the siteaccess part, if needed.
     *
     * @param string $uri The original URI
     *
     * @return string
     */
    public function analyseURI($uri)
    {
        if ($this->key === null) {
            return $uri;
        }

        return parent::analyseURI($uri);
    }

    public function analyseLink($linkUri)
    {
        if ($this->key === null) {
            return $linkUri;
        }

        return parent::analyseLink($linkUri);
    }

    public function reverseMatch($siteAccessName)
    {
        $publicAccess = $this->handler->load($siteAccessName);
        if ($publicAccess === null) {
            return null;
        }

        $matcherConfiguration = $publicAccess->getMatcherConfiguration();
        $this->request->setHost($matcherConfiguration->host);
        if ($matcherConfiguration->path !== null) {
            $this->setMapKey($matcherConfiguration->path);
        }

        return $this;
    }
}

class_alias(SiteAccessMatcher::class, 'EzSystems\EzPlatformSiteFactory\SiteAccessMatcher');
