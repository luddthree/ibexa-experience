<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Service\ModelBuild;

use Ibexa\Personalization\Client\Consumer\ModelBuild\ModelBuildDataFetcherInterface;
use Ibexa\Personalization\Client\Consumer\ModelBuild\ModelBuildDataSenderInterface;
use Ibexa\Personalization\Exception\BadResponseException;
use Ibexa\Personalization\Service\Setting\SettingServiceInterface;
use Ibexa\Personalization\Value\ModelBuild\BuildReport;
use Ibexa\Personalization\Value\ModelBuild\ModelBuildStatus;
use Symfony\Component\HttpFoundation\Response;

/**
 * @internal
 */
final class ModelBuildService implements ModelBuildServiceInterface
{
    private const DEFAULT_LAST_BUILDS_NUMBER = 1;

    private ModelBuildDataFetcherInterface $modelBuildDataFetcher;

    private ModelBuildDataSenderInterface $modelBuildDataSender;

    private SettingServiceInterface $settingService;

    public function __construct(
        ModelBuildDataFetcherInterface $modelBuildDataFetcher,
        ModelBuildDataSenderInterface $modelBuildDataSender,
        SettingServiceInterface $settingService
    ) {
        $this->modelBuildDataFetcher = $modelBuildDataFetcher;
        $this->modelBuildDataSender = $modelBuildDataSender;
        $this->settingService = $settingService;
    }

    /**
     * @throws \Ibexa\Personalization\Exception\BadResponseException
     * @throws \JsonException
     */
    public function triggerModelBuild(int $customerId, string $referenceCode): ?BuildReport
    {
        try {
            $response = $this->modelBuildDataSender->triggerModelBuild(
                $customerId,
                $this->settingService->getLicenceKeyByCustomerId($customerId),
                $referenceCode
            );

            return BuildReport::fromArray(
                json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR)
            );
        } catch (BadResponseException $exception) {
            if (Response::HTTP_SERVICE_UNAVAILABLE === $exception->getCode()) {
                return null;
            }

            throw $exception;
        }
    }

    /**
     * @throws \Ibexa\Personalization\Exception\BadResponseException
     * @throws \JsonException
     */
    public function getModelBuildStatus(
        int $customerId,
        string $referenceCode,
        int $lastBuildsNumber = self::DEFAULT_LAST_BUILDS_NUMBER
    ): ?ModelBuildStatus {
        try {
            $response = $this->modelBuildDataFetcher->getModelBuildStatus(
                $customerId,
                $this->settingService->getLicenceKeyByCustomerId($customerId),
                $referenceCode,
                $lastBuildsNumber
            );

            $responseContents = json_decode(
                $response->getBody()->getContents(),
                true,
                512,
                JSON_THROW_ON_ERROR
            );

            if (empty($responseContents)) {
                return null;
            }

            return ModelBuildStatus::fromArray($responseContents);
        } catch (BadResponseException $exception) {
            $allowedHttpCodes = [
                Response::HTTP_NOT_FOUND,
                Response::HTTP_SERVICE_UNAVAILABLE,
            ];

            if (in_array($exception->getCode(), $allowedHttpCodes, true)) {
                return null;
            }

            throw $exception;
        }
    }
}
