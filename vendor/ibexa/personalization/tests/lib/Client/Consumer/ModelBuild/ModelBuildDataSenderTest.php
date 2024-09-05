<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Client\Consumer\ModelBuild;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use Ibexa\Personalization\Client\Consumer\ModelBuild\ModelBuildDataSender;
use Ibexa\Personalization\Client\Consumer\ModelBuild\ModelBuildDataSenderInterface;
use Ibexa\Tests\Personalization\Client\Consumer\AbstractConsumerTestCase;
use Ibexa\Tests\Personalization\Fixture\Loader;

/**
 * @covers \Ibexa\Personalization\Client\Consumer\ModelBuild\ModelBuildDataSender
 */
final class ModelBuildDataSenderTest extends AbstractConsumerTestCase
{
    private ModelBuildDataSenderInterface $modelBuildDataSender;

    private string $body;

    public function setUp(): void
    {
        parent::setUp();

        $this->modelBuildDataSender = new ModelBuildDataSender(
            $this->client,
            'fake.endpoint.com'
        );

        $this->body = Loader::load(Loader::TRIGGER_MODEL_BUILD_FIXTURE);
    }

    public function testTriggerModelBuild(): void
    {
        $this->mockClientSendRequest();

        $fetchedResponse = $this->modelBuildDataSender->triggerModelBuild(
            12345,
            '12345-12345-12345-12345',
            'foo'
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
                'PUT',
                new Uri('fake.endpoint.com/api/v3/12345/modelbuild/trigger_modelbuild/foo'),
                [
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
