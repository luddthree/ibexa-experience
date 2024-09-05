<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Fastly\ProxyClient;

use FOS\HttpCache\ProxyClient\Dispatcher;
use FOS\HttpCache\ProxyClient\HttpProxyClient;
use FOS\HttpCache\ProxyClient\Invalidation\PurgeCapable;
use FOS\HttpCache\ProxyClient\Invalidation\TagCapable;
use Http\Message\RequestFactory;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;

/**
 * Fastly HTTP cache invalidator.
 *
 * Since https://github.com/ezsystems/ezplatform-http-cache/commit/ee8f9f3abdcfb50cb11cc6a44b54e28d304c0349 we need to
 * implement BanInterface too, or FOS\HttpCache\Handler will complain that invalidator do not support CacheInvalidator::INVALIDATE
 */
class Fastly extends HttpProxyClient implements TagCapable, PurgeCapable
{
    protected const HTTP_METHOD_PURGE = 'POST';
    public const TAG_HEADER_NAME = 'Surrogate-Key';
    public const FASTLY_KEY_HEADER_NAME = 'Fastly-Key';

    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface */
    private $configResolver;

    public function __construct(
        ConfigResolverInterface $configResolver,
        Dispatcher $httpDispatcher,
        array $options = [],
        RequestFactory $messageFactory = null
    ) {
        parent::__construct($httpDispatcher, $options, $messageFactory);
        $this->configResolver = $configResolver;
    }

    public function getFastlyServiceId()
    {
        return $this->configResolver->getParameter('http_cache.fastly.service_id');
    }

    public function getFastlyKey()
    {
        return $this->configResolver->getParameter('http_cache.fastly.key');
    }

    /**
     * Remove/Expire cache objects based on cache tags.
     *
     * @param array $tags Tags that should be removed/expired from the cache
     *
     * @return $this
     */
    public function invalidateTags(array $tags)
    {
        $headers = $this->getHeaders();

        // We can purge up to 256 tags at a time, also using soft purge (confirmed by Fastly)
        // https://docs.fastly.com/api/purge#purge_db35b293f8a724717fcf25628d713583
        foreach (array_chunk($tags, 256) as $tagchunk) {
            $headers[self::TAG_HEADER_NAME] = implode(' ', $tagchunk);
            $this->queueRequest(
                self::HTTP_METHOD_PURGE,
                '/service/' . $this->getFastlyServiceId() . '/purge',
                $headers,
                false
            );
        }

        return $this;
    }

    public function purge($url, array $headers = [])
    {
        $this->queueRequest(self::HTTP_METHOD_PURGE, $url, $headers);

        return $this;
    }

    protected function getHeaders(): array
    {
        $headers = [
            'Fastly-Soft-Purge' => '1',
            'Accept' => 'application/json',
        ];

        if ($fastlyKey = $this->getFastlyKey()) {
            $headers['Fastly-Key'] = $fastlyKey;
        }

        return $headers;
    }
}

class_alias(Fastly::class, 'EzSystems\PlatformFastlyCacheBundle\ProxyClient\Fastly');
