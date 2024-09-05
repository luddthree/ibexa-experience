<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Personalization\Service\Content;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Ibexa\Personalization\Service\Content\ContentServiceInterface;
use Ibexa\Tests\Integration\Personalization\BaseIntegrationTestCase;

/**
 * @phpstan-type Packages array<array{
 *     contentTypeId: int,
 *     contentTypeName: string,
 *     lang: string,
 *     uri: string,
 * }>
 */
abstract class BaseContentServiceTestCase extends BaseIntegrationTestCase
{
    protected ContentServiceInterface $personalizationContentService;

    protected MockHandler $mockHandler;

    protected function setUp(): void
    {
        parent::setUp();

        if (!$this->hasInstallationKey()) {
            $this->setInstallationKey();
        }

        $this->mockHandler = self::getServiceByClassName(
            MockHandler::class,
            'ibexa.personalization.http_client_handler_mock.test'
        );

        $this->mockHandler->append(new Response(202, [], '{"isAccepted": true, "acceptor": null}'));
        $this->mockHandler->append(new Response(202, [], '{"isAccepted": true, "acceptor": null}'));
        $this->mockHandler->append(new Response(202, []));

        $this->personalizationContentService = self::getServiceByClassName(ContentServiceInterface::class);
    }

    /**
     * @param array<string> $remoteIds
     *
     * @return array<\Ibexa\Contracts\Core\Repository\Values\Content\Content>
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function loadContentItems(array $remoteIds): array
    {
        $contentItems = [];
        foreach ($remoteIds as $remoteId) {
            $contentItems[] = $this->contentService->loadContentByRemoteId($remoteId);
        }

        return $contentItems;
    }

    /**
     * @return array{
     *     packages: Packages,
     *     accessToken: string,
     *     importHeaders: array<string, string>,
     * }
     *
     * @throws \JsonException
     */
    public function getLastRequestBody(): array
    {
        return json_decode(
            $this->mockHandler->getLastRequest()->getBody()->getContents(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
    }

    /**
     * @return array<int|string>
     */
    public function getLastAuthOptions(): array
    {
        return $this->mockHandler->getLastOptions()['auth'];
    }

    /**
     * @param array<int|string> $expectedAuthCredentials
     * @param int $expectedPackageItemsCount
     * @param array<int, array<string>> $expectedUpdatedContentTypeIdsWithLanguages
     */
    public function assertRequestHasBeenSent(
        array $expectedAuthCredentials,
        int $expectedPackageItemsCount,
        array $expectedUpdatedContentTypeIdsWithLanguages
    ): void {
        /** @phpstan-var Packages $packages */
        $packages = $this->getLastRequestBody()['packages'];

        $updatedContentTypeIds = [];
        foreach ($packages as $package) {
            $updatedContentTypeIds[$package['contentTypeId']][] = $package['lang'];
        }

        self::assertEquals($expectedAuthCredentials, $this->getLastAuthOptions());
        self::assertSame($expectedPackageItemsCount, count($packages));
        self::assertEquals($expectedUpdatedContentTypeIdsWithLanguages, $updatedContentTypeIds);
    }
}
