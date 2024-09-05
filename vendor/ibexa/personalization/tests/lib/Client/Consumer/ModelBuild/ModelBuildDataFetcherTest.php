<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Client\Consumer\ModelBuild;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use Ibexa\Personalization\Client\Consumer\ModelBuild\ModelBuildDataFetcher;
use Ibexa\Personalization\Client\Consumer\ModelBuild\ModelBuildDataFetcherInterface;
use Ibexa\Tests\Personalization\Client\Consumer\AbstractConsumerTestCase;
use Ibexa\Tests\Personalization\Fixture\Loader;

/**
 * @covers \Ibexa\Personalization\Client\Consumer\ModelBuild\ModelBuildDataFetcher
 */
final class ModelBuildDataFetcherTest extends AbstractConsumerTestCase
{
    private ModelBuildDataFetcherInterface $modelBuildDataFetcher;

    private string $body;

    public function setUp(): void
    {
        parent::setUp();

        $this->modelBuildDataFetcher = new ModelBuildDataFetcher(
            $this->client,
            'fake.endpoint.com'
        );

        $this->body = Loader::load(Loader::MODEL_BUILD_STATUS_FIXTURE);
    }

    public function testGetModelBuildStatus(): void
    {
        $this->mockClientSendRequest();

        $fetchedResponse = $this->modelBuildDataFetcher->getModelBuildStatus(
            12345,
            '12345-12345-12345-12345',
            'foo',
            1
        );

        $response = new Response(
            200,
            [],
            $this->body
        );

        self::assertEquals(
            $response->getBody()->getContents(),
            $fetchedResponse->getBody()->getContents()
        );
    }

    private function mockClientSendRequest(): void
    {
        $this->client
            ->expects(self::once())
            ->method('sendRequest')
            ->with(
                'GET',
                new Uri('fake.endpoint.com/api/v3/12345/modelbuild/get_model/foo'),
                [
                    'query' => [
                        'lastBuildsNum' => 1,
                    ],
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                ]
            )
            ->willReturn(
                new Response(
                    200,
                    [],
                    $this->body
                )
            );
    }
}
