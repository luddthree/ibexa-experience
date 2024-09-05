<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ConnectorQualifio;

use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class MockHttpClientCallback
{
    /**
     * @param array<mixed> $options
     */
    public function __invoke(string $method, string $url, array $options = []): ResponseInterface
    {
        $file = $this->mapToUrlToFile($method, $url, $options);

        $content = file_get_contents($file);
        if (false === $content) {
            throw new \LogicException(sprintf(
                '%s file does not exist or cannot be loaded.',
                $file,
            ));
        }

        $info = [];
        $info['response_headers']['content-type'] = 'application/json';

        return new MockResponse($content, $info);
    }

    /**
     * @param array<string, mixed> $options
     */
    private function mapToUrlToFile(string $method, string $url, array $options = []): string
    {
        switch (true) {
            case 'https://api.qualif.io/v1/campaignfeed/channels/FAKE-CHANNEL/json?clientId=42&cursor=1265523' === $url:
                return __DIR__ . '/_fixtures/campaignfeed_channels_FAKE-CHANNEL_json.client_id_42.cursor_1265523.json';
            case 'https://api.qualif.io/v1/campaignfeed/channels/FAKE-CHANNEL/json?clientId=42&cursor=1420946' === $url:
                return __DIR__ . '/_fixtures/campaignfeed_channels_FAKE-CHANNEL_json.client_id_42.cursor_1420946.json';
            case 'https://api.qualif.io/v1/campaignfeed/channels/FAKE-CHANNEL/json?clientId=42' === $url:
                return __DIR__ . '/_fixtures/campaignfeed_channels_FAKE-CHANNEL_json.client_id_42.json';
            default:
                throw new \InvalidArgumentException(sprintf(
                    'Unknown URL %s, cannot match to a file. Add a corresponding entry %s in %s',
                    $url,
                    __METHOD__,
                    __LINE__ - 5,
                ));
        }
    }
}
