<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Dashboard\News;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use SimpleXMLElement;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @internal
 */
final class Feed implements FeedInterface
{
    private NewsMapperInterface $newsMapper;

    private HttpClientInterface $client;

    private LoggerInterface $logger;

    public function __construct(
        NewsMapperInterface $newsMapper,
        HttpClientInterface $client,
        ?LoggerInterface $logger = null
    ) {
        $this->newsMapper = $newsMapper;
        $this->client = $client;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @return \Ibexa\Dashboard\News\Values\NewsItem[]
     *
     * @throws \Exception
     * @throws \Symfony\Contracts\HttpClient\Exception\ExceptionInterface
     * @throws \Ibexa\Dashboard\News\FeedException
     */
    public function fetch(string $url, int $limit): array
    {
        $this->logger->debug('Fetching Ibexa News RSS', ['url' => $url, $limit]);

        $feed = $this->loadFeed($url);

        return $this->getNews($feed, $limit);
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\ExceptionInterface
     * @throws \Ibexa\Dashboard\News\FeedException
     */
    private function loadFeed(string $url): SimpleXMLElement
    {
        try {
            $response = $this->client->request('GET', $url);
        } catch (TransportExceptionInterface $e) {
            throw new FeedException($e->getMessage());
        }

        $feed = simplexml_load_string($response->getContent());
        if ($feed === false) {
            throw new FeedException('Cannot convert a string of XML into an object');
        }

        if (!$feed->channel) {
            throw new FeedException('Invalid channel');
        }

        return $feed;
    }

    /**
     * @return \Ibexa\Dashboard\News\Values\NewsItem[]
     *
     * @throws \Exception
     */
    private function getNews(
        SimpleXMLElement $feed,
        int $limit
    ): array {
        $news = [];
        $i = 0;
        foreach ($feed->channel->item as $item) {
            $news[] = $this->newsMapper->map($item);
            ++$i;
            if ($i >= $limit) {
                break;
            }
        }

        return $news;
    }
}
