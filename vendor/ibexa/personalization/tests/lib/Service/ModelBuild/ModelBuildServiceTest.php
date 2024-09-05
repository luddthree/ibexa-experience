<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Service\ModelBuild;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response as GuzzleHttpResponse;
use Ibexa\Personalization\Client\Consumer\ModelBuild\ModelBuildDataFetcherInterface;
use Ibexa\Personalization\Client\Consumer\ModelBuild\ModelBuildDataSenderInterface;
use Ibexa\Personalization\Exception\BadResponseException;
use Ibexa\Personalization\Service\ModelBuild\ModelBuildService;
use Ibexa\Personalization\Service\ModelBuild\ModelBuildServiceInterface;
use Ibexa\Personalization\Value\ModelBuild\BuildReport;
use Ibexa\Personalization\Value\ModelBuild\ModelBuildStatus;
use Ibexa\Tests\Personalization\Fixture\Loader;
use Ibexa\Tests\Personalization\Service\AbstractServiceTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @covers \Ibexa\Personalization\Service\ModelBuild\ModelBuildService
 */
final class ModelBuildServiceTest extends AbstractServiceTestCase
{
    private ModelBuildServiceInterface $modelBuildService;

    /** @var \Ibexa\Personalization\Client\Consumer\ModelBuild\ModelBuildDataFetcherInterface|\PHPUnit\Framework\MockObject\MockObject */
    private ModelBuildDataFetcherInterface $modelBuildDataFetcher;

    /** @var \Ibexa\Personalization\Client\Consumer\ModelBuild\ModelBuildDataSenderInterface|\PHPUnit\Framework\MockObject\MockObject */
    private ModelBuildDataSenderInterface $modelBuildDataSender;

    public function setUp(): void
    {
        parent::setUp();

        $this->modelBuildDataFetcher = $this->createMock(ModelBuildDataFetcherInterface::class);
        $this->modelBuildDataSender = $this->createMock(ModelBuildDataSenderInterface::class);
        $this->modelBuildService = new ModelBuildService(
            $this->modelBuildDataFetcher,
            $this->modelBuildDataSender,
            $this->settingService
        );
    }

    /**
     * @dataProvider provideDataForTestTriggerModelBuild
     *
     * @throws \Ibexa\Personalization\Exception\BadResponseException
     * @throws \JsonException
     */
    public function testTriggerModelBuild(
        ?BuildReport $buildReport,
        GuzzleHttpResponse $response
    ): void {
        $this->getLicenseKey();
        $this->mockModelBuildDataSenderTriggerModelBuild($response);

        self::assertEquals(
            $buildReport,
            $this->modelBuildService->triggerModelBuild(12345, 'foo')
        );
    }

    /**
     * @dataProvider provideDataForTestGetModelBuildStatus
     *
     * @throws \Ibexa\Personalization\Exception\BadResponseException
     * @throws \JsonException
     */
    public function testGetModelBuildStatus(
        ?ModelBuildStatus $modelBuildStatus,
        GuzzleHttpResponse $response
    ): void {
        $this->getLicenseKey();
        $this->mockModelBuildDataFetcherGetModelBuildStatus($response);

        self::assertEquals(
            $modelBuildStatus,
            $this->modelBuildService->getModelBuildStatus(12345, 'foo')
        );
    }

    /**
     * @return iterable<array{
     *     \Ibexa\Personalization\Value\ModelBuild\BuildReport|null,
     *     \GuzzleHttp\Psr7\Response
     * }>
     */
    public function provideDataForTestTriggerModelBuild(): iterable
    {
        yield [
            new BuildReport(0, 'NEW'),
            new GuzzleHttpResponse(
                200,
                [],
                Loader::load(Loader::TRIGGER_MODEL_BUILD_FIXTURE),
            ),
        ];

        yield [
            null,
            new GuzzleHttpResponse(503),
        ];
    }

    /**
     * @return iterable<array{
     *     \Ibexa\Personalization\Value\ModelBuild\ModelBuildStatus|null,
     *     \GuzzleHttp\Psr7\Response
     * }>
     *
     * @throws \Exception
     */
    public function provideDataForTestGetModelBuildStatus(): iterable
    {
        yield [
            ModelBuildStatus::fromArray($this->getModelBuildStatusBody()),
            new GuzzleHttpResponse(
                200,
                [],
                Loader::load(Loader::MODEL_BUILD_STATUS_FIXTURE),
            ),
        ];

        yield [
            null,
            new GuzzleHttpResponse(
                200,
                [],
                '{ }',
            ),
        ];

        yield [
            null,
            new GuzzleHttpResponse(503),
        ];

        yield [
            null,
            new GuzzleHttpResponse(404),
        ];
    }

    private function mockModelBuildDataSenderTriggerModelBuild(GuzzleHttpResponse $response): void
    {
        $statusCode = $response->getStatusCode();
        $dataSender = $this->modelBuildDataSender
            ->expects(self::once())
            ->method('triggerModelBuild')
            ->with(12345, '12345-12345-12345-12345', 'foo');

        if (Response::HTTP_OK === $statusCode) {
            $dataSender->willReturn($response);
        }

        if (Response::HTTP_SERVICE_UNAVAILABLE === $statusCode) {
            $dataSender->willThrowException(
                new BadResponseException(
                    'Service Unavailable',
                    new Request('PUT', '/api/v3/12345/modelbuild/trigger_modelbuild/foo'),
                    $response
                )
            );
        }
    }

    private function mockModelBuildDataFetcherGetModelBuildStatus(GuzzleHttpResponse $response): void
    {
        $statusCode = $response->getStatusCode();
        $dataFetcher = $this->modelBuildDataFetcher
            ->expects(self::once())
            ->method('getModelBuildStatus')
            ->with(12345, '12345-12345-12345-12345', 'foo', 1);

        if (Response::HTTP_OK === $statusCode) {
            $dataFetcher->willReturn($response);
        }

        if (Response::HTTP_SERVICE_UNAVAILABLE === $statusCode) {
            $dataFetcher->willThrowException(
                new BadResponseException(
                    'Service Unavailable',
                    new Request('GET', '/api/v3/12345/modelbuild/get_model/foo'),
                    $response
                )
            );
        }

        if (Response::HTTP_NOT_FOUND === $statusCode) {
            $dataFetcher->willThrowException(
                new BadResponseException(
                    'Not found',
                    new Request('GET', '/api/v3/12345/modelbuild/get_model/foo'),
                    $response
                )
            );
        }
    }

    /**
     * @return array{
     *     'referenceCode': string,
     *     'buildReports': array<array{
     *          'queueTime': string,
     *          'startTime': null,
     *          'finishTime': null,
     *          'numberOfItems': int,
     *          'taskUuid': string,
     *          'state': string,
     *      }>,
     * }
     *
     * @throws \Exception
     */
    private function getModelBuildStatusBody(): array
    {
        return json_decode(
            Loader::load(Loader::MODEL_BUILD_STATUS_FIXTURE),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
    }
}
