<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Personalization\Service\Content;

final class ContentServiceTest extends BaseContentServiceTestCase
{
    /**
     * @dataProvider provideDataForTestUpdateContent
     *
     * @param array<int|string> $expectedAuthCredentials
     * @param array<string, array<string>> $expectedUpdatedContentTypeWithLanguages
     * @param array<string> $languageCodes
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function testUpdateContent(
        array $expectedAuthCredentials,
        int $expectedPackageItemsCount,
        array $expectedUpdatedContentTypeWithLanguages,
        string $remoteId,
        array $languageCodes
    ): void {
        $this->personalizationContentService->updateContent(
            $this->contentService->loadContentByRemoteId($remoteId, $languageCodes),
            $languageCodes
        );

        $this->assertRequestHasBeenSent(
            $expectedAuthCredentials,
            $expectedPackageItemsCount,
            $this->mapToContentTypeIdWithLanguages($expectedUpdatedContentTypeWithLanguages),
        );
    }

    /**
     * @dataProvider provideContentItems
     *
     * @param array<int|string> $expectedAuthCredentials
     * @param array<string, array<string>> $expectedUpdatedContentTypeWithLanguages
     * @param array<string> $remoteIds
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function testUpdateContentItems(
        array $expectedAuthCredentials,
        int $expectedPackageItemsCount,
        array $expectedUpdatedContentTypeWithLanguages,
        array $remoteIds
    ): void {
        $this->personalizationContentService->updateContentItems(
            $this->loadContentItems($remoteIds)
        );

        $this->assertRequestHasBeenSent(
            $expectedAuthCredentials,
            $expectedPackageItemsCount,
            $this->mapToContentTypeIdWithLanguages($expectedUpdatedContentTypeWithLanguages),
        );
    }

    /**
     * @dataProvider provideDataForTestDeleteContent
     *
     * @param array<int|string> $expectedAuthCredentials
     * @param array<string, array<string>> $expectedUpdatedContentTypeWithLanguages
     * @param array<string> $languageCodes
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function testDeleteContent(
        array $expectedAuthCredentials,
        int $expectedPackageItemsCount,
        array $expectedUpdatedContentTypeWithLanguages,
        string $remoteId,
        array $languageCodes
    ): void {
        $this->personalizationContentService->deleteContent(
            $this->contentService->loadContentByRemoteId($remoteId),
            $languageCodes
        );

        $this->assertRequestHasBeenSent(
            $expectedAuthCredentials,
            $expectedPackageItemsCount,
            $this->mapToContentTypeIdWithLanguages($expectedUpdatedContentTypeWithLanguages),
        );
    }

    /**
     * @dataProvider provideContentItems
     *
     * @param array<int|string> $expectedAuthCredentials
     * @param array<string, array<string>> $expectedUpdatedContentTypeWithLanguages
     * @param array<string> $remoteIds
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function testDeleteContentItems(
        array $expectedAuthCredentials,
        int $expectedPackageItemsCount,
        array $expectedUpdatedContentTypeWithLanguages,
        array $remoteIds
    ): void {
        $this->personalizationContentService->deleteContentItems(
            $this->loadContentItems($remoteIds)
        );

        $this->assertRequestHasBeenSent(
            $expectedAuthCredentials,
            $expectedPackageItemsCount,
            $this->mapToContentTypeIdWithLanguages($expectedUpdatedContentTypeWithLanguages),
        );
    }

    /**
     * @phpstan-return iterable<array{
     *     array<int|string>,
     *     int,
     *     array<string, array<string>>,
     *     string,
     *     array<string>
     * }>
     */
    public function provideDataForTestUpdateContent(): iterable
    {
        yield 'Article in English version' => [
            [
                self::CUSTOMER_ID_SITE,
                self::LICENSE_KEY_SITE,
            ],
            1,
            [
                self::CONTENT_TYPE_ARTICLE => [
                    self::LANGUAGE_CODE_ENG,
                ],
            ],
            self::REMOTE_ID_MAIN_PERSO_ARTICLE,
            [self::LANGUAGE_CODE_ENG],
        ];

        yield 'Article in Polish version' => [
            [
                self::CUSTOMER_ID_SITE_PL,
                self::LICENSE_KEY_SITE_PL,
            ],
            1,
            [
                self::CONTENT_TYPE_ARTICLE => [
                    self::LANGUAGE_CODE_POL,
                ],
            ],
            self::REMOTE_ID_MAIN_PERSO_ARTICLE,
            [self::LANGUAGE_CODE_POL],
        ];

        yield 'Article with 2 language versions - tracked each language on dedicated account' => [
            [
                self::CUSTOMER_ID_SITE_PL,
                self::LICENSE_KEY_SITE_PL,
            ],
            1,
            [
                self::CONTENT_TYPE_ARTICLE => [
                    self::LANGUAGE_CODE_POL,
                ],
            ],
            self::REMOTE_ID_MAIN_PERSO_ARTICLE,
            [
                self::LANGUAGE_CODE_ENG,
                self::LANGUAGE_CODE_POL,
            ],
        ];

        yield 'Console with 2 language versions - tracked all languages in one account' => [
            [
                self::CUSTOMER_ID_CONSOLE_SHOP,
                self::LICENSE_KEY_CONSOLE_SHOP,
            ],
            2,
            [
                self::CONTENT_TYPE_CONSOLE => [
                    self::LANGUAGE_CODE_ENG,
                    self::LANGUAGE_CODE_POL,
                ],
            ],
            self::REMOTE_ID_CONSOLE_XBOX_SERIES_X,
            [
                self::LANGUAGE_CODE_ENG,
                self::LANGUAGE_CODE_POL,
            ],
        ];
    }

    /**
     * @phpstan-return iterable<array{
     *     array<int|string>,
     *     int,
     *     array<string, array<string>>,
     *     string,
     *     array<string>,
     * }>
     */
    public function provideDataForTestDeleteContent(): iterable
    {
        yield 'Article with 2 language versions - tracked each language on dedicated account' => [
            [
                self::CUSTOMER_ID_SITE_PL,
                self::LICENSE_KEY_SITE_PL,
            ],
            1,
            [
                self::CONTENT_TYPE_ARTICLE => [
                    self::LANGUAGE_CODE_POL,
                ],
            ],
            self::REMOTE_ID_MAIN_PERSO_ARTICLE,
            [
                self::LANGUAGE_CODE_POL,
            ],
        ];

        yield 'Remove console in Polish - tracked all languages in one account' => [
            [
                self::CUSTOMER_ID_CONSOLE_SHOP,
                self::LICENSE_KEY_CONSOLE_SHOP,
            ],
            1,
            [
                self::CONTENT_TYPE_CONSOLE => [
                    self::LANGUAGE_CODE_POL,
                ],
            ],
            self::REMOTE_ID_CONSOLE_XBOX_SERIES_X,
            [
                self::LANGUAGE_CODE_POL,
            ],
        ];

        yield 'Console with 2 language versions - tracked all languages in one account' => [
            [
                self::CUSTOMER_ID_CONSOLE_SHOP,
                self::LICENSE_KEY_CONSOLE_SHOP,
            ],
            2,
            [
                self::CONTENT_TYPE_CONSOLE => [
                    self::LANGUAGE_CODE_ENG,
                    self::LANGUAGE_CODE_POL,
                ],
            ],
            self::REMOTE_ID_CONSOLE_XBOX_SERIES_X,
            [
                self::LANGUAGE_CODE_ENG,
                self::LANGUAGE_CODE_POL,
            ],
        ];
    }

    /**
     * @return iterable<array{
     *     array<int|string>,
     *     int,
     *     array<string, array<string>>,
     *     array<string>
     * }>
     */
    public function provideContentItems(): iterable
    {
        yield 'Update 2 Articles in English tracked all in one account' => [
            [
                self::CUSTOMER_ID_SITE,
                self::LICENSE_KEY_SITE,
            ],
            1,
            [
                self::CONTENT_TYPE_ARTICLE => [
                    self::LANGUAGE_CODE_ENG,
                ],
            ],
            [
                self::REMOTE_ID_PERSO_ARTICLE_ENG_1,
                self::REMOTE_ID_PERSO_ARTICLE_ENG_2,
            ],
        ];

        yield 'Update 2 Articles in Polish tracked all in one account' => [
            [
                self::CUSTOMER_ID_SITE_PL,
                self::LICENSE_KEY_SITE_PL,
            ],
            1,
            [
                self::CONTENT_TYPE_ARTICLE => [
                    self::LANGUAGE_CODE_POL,
                ],
            ],
            [self::REMOTE_ID_MAIN_PERSO_ARTICLE],
        ];

        yield 'Update 2 Articles in English and Polish tracked by different accounts' => [
            [
                self::CUSTOMER_ID_SITE_PL,
                self::LICENSE_KEY_SITE_PL,
            ],
            1,
            [
                self::CONTENT_TYPE_ARTICLE => [
                    self::LANGUAGE_CODE_POL,
                ],
            ],
            [self::REMOTE_ID_MAIN_PERSO_ARTICLE],
        ];

        yield 'Update 4 content items tracked all in one account' => [
            [
                self::CUSTOMER_ID_CONSOLE_SHOP,
                self::LICENSE_KEY_CONSOLE_SHOP,
            ],
            4,
            [
                self::CONTENT_TYPE_CONSOLE => [
                    self::LANGUAGE_CODE_ENG,
                    self::LANGUAGE_CODE_POL,
                ],
                self::CONTENT_TYPE_LAPTOP => [
                    self::LANGUAGE_CODE_ENG,
                    self::LANGUAGE_CODE_POL,
                ],
            ],
            [
                self::REMOTE_ID_CONSOLE_XBOX_SERIES_X,
                self::REMOTE_ID_CONSOLE_PLAY_STATION_5,
                self::REMOTE_ID_LAPTOP_MAC_BOOK_PRO_2024,
                self::REMOTE_ID_LAPTOP_DELL_5510,
            ],
        ];
    }

    /**
     * @param array<string, array<string>> $expectedUpdatedContentTypeWithLanguages
     *
     * @return array<int, array<string>>
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    private function mapToContentTypeIdWithLanguages(array $expectedUpdatedContentTypeWithLanguages): array
    {
        $expectedUpdatedContentTypeIdWithLanguages = [];
        foreach ($expectedUpdatedContentTypeWithLanguages as $contentTypeIdentifier => $languageCodes) {
            $contentType = $this->contentTypeService->loadContentTypeByIdentifier($contentTypeIdentifier);
            $expectedUpdatedContentTypeIdWithLanguages[(int)$contentType->id] = $languageCodes;
        }

        return $expectedUpdatedContentTypeIdWithLanguages;
    }
}
