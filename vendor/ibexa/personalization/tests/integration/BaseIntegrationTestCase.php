<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Personalization;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\LanguageService;
use Ibexa\Contracts\Core\Repository\SettingService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\Setting\SettingCreateStruct;

abstract class BaseIntegrationTestCase extends IbexaKernelTestCase
{
    protected const CUSTOMER_ID_SITE = 12345;
    protected const LICENSE_KEY_SITE = '12345-12345-12345-12345';
    protected const CUSTOMER_ID_SITE_PL = 56789;
    protected const LICENSE_KEY_SITE_PL = '12345-56789-12345-56789';
    protected const CUSTOMER_ID_CONSOLE_SHOP = 13579;
    protected const LICENSE_KEY_CONSOLE_SHOP = '13579-24680-12457-39812';

    protected const CONTENT_TYPE_ARTICLE = 'article';
    protected const REMOTE_ID_MAIN_PERSO_ARTICLE = 'main_perso_article';
    protected const REMOTE_ID_PERSO_ARTICLE_ENG_1 = 'perso_article_eng_1';
    protected const REMOTE_ID_PERSO_ARTICLE_ENG_2 = 'perso_article_eng_2';

    protected const CONTENT_TYPE_BLOG = 'blog';
    protected const CONTENT_TYPE_CONSOLE = 'console';
    protected const REMOTE_ID_CONSOLE_XBOX_SERIES_X = 'console_xbox_series_x';
    protected const REMOTE_ID_CONSOLE_PLAY_STATION_5 = 'console_play_station_5';

    protected const CONTENT_TYPE_LAPTOP = 'laptop';
    protected const REMOTE_ID_LAPTOP_MAC_BOOK_PRO_2024 = 'laptop_apple_macbook_pro_2024';
    protected const REMOTE_ID_LAPTOP_DELL_5510 = 'laptop_dell_5510';

    protected const CONTENT_TYPE_SMARTPHONE = 'smartphone';

    protected const LANGUAGE_CODE_ENG = 'eng-GB';
    protected const LANGUAGE_CODE_POL = 'pol-PL';

    protected const CONTENT_TYPE_GROUP_CONTENT = 1;

    private const SETTING_GROUP = 'personalization';
    private const SETTING_IDENTIFIER = 'installation_key';

    protected ContentService $contentService;

    protected ContentTypeService $contentTypeService;

    protected LanguageService $languageService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->contentService = self::getContentService();
        $this->contentTypeService = self::getContentTypeService();
        $this->languageService = self::getLanguageService();

        self::setAdministratorUser();
    }

    protected function createTestContent(
        ContentType $contentType,
        string $languageCode,
        int $parentLocationId = 2
    ): Content {
        $createStruct = $this->contentService->newContentCreateStruct(
            $contentType,
            $languageCode
        );

        $content = $this->contentService->createContent(
            $createStruct,
            [self::getLocationService()->newLocationCreateStruct($parentLocationId)]
        );

        return $this->contentService->publishVersion($content->getVersionInfo());
    }

    /**
     * @param array<string, string> $names
     */
    protected function createTestContentType(
        string $identifier,
        string $languageCode,
        array $names
    ): ContentType {
        $contentTypeCreateStruct = $this->contentTypeService->newContentTypeCreateStruct($identifier);
        $contentTypeCreateStruct->mainLanguageCode = $languageCode;
        $contentTypeCreateStruct->names = $names;
        $contentTypeCreateStruct->nameSchema = '<name>';

        $contentTypeDraft = $this->contentTypeService->createContentType(
            $contentTypeCreateStruct,
            [$this->contentTypeService->loadContentTypeGroup(self::CONTENT_TYPE_GROUP_CONTENT)]
        );

        $this->contentTypeService->addFieldDefinition(
            $contentTypeDraft,
            $this->contentTypeService->newFieldDefinitionCreateStruct('name', 'ezstring')
        );

        $this->contentTypeService->publishContentTypeDraft($contentTypeDraft);

        return $contentTypeDraft;
    }

    protected function createLanguage(
        string $name,
        string $languageCode
    ): Language {
        $langCreateStruct = self::getLanguageService()->newLanguageCreateStruct();
        $langCreateStruct->name = $name;
        $langCreateStruct->languageCode = $languageCode;

        return $this->languageService->createLanguage($langCreateStruct);
    }

    /**
     * @param array<string, array<string, mixed>> $fields
     * @param array<\Ibexa\Contracts\Core\Repository\Values\Content\LocationCreateStruct> $locationCreateStructs
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ContentFieldValidationException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ContentValidationException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    protected function createTestContentItemWithFields(
        ContentCreateStruct $createStruct,
        array $fields,
        array $locationCreateStructs = []
    ): Content {
        foreach ($fields as $fieldDefIdentifier => $field) {
            foreach ($field as $languageCode => $value) {
                $createStruct->setField($fieldDefIdentifier, $value, $languageCode);
            }
        }

        if (empty($locationCreateStructs)) {
            $locationCreateStructs[] = self::getLocationService()->newLocationCreateStruct(2);
        }
        $content = $this->contentService->createContent(
            $createStruct,
            $locationCreateStructs
        );

        return $this->contentService->publishVersion($content->getVersionInfo());
    }

    protected function hasInstallationKey(): bool
    {
        try {
            $coreSettingService = self::getServiceByClassName(SettingService::class);
            $setting = $coreSettingService->loadSetting(
                self::SETTING_GROUP,
                self::SETTING_IDENTIFIER
            );

            return !empty($setting->value);
        } catch (NotFoundException $exception) {
            return false;
        }
    }

    protected function setInstallationKey(): void
    {
        $coreSettingService = self::getServiceByClassName(SettingService::class);
        $settingCreateStruct = new SettingCreateStruct(
            [
                'group' => self::SETTING_GROUP,
                'identifier' => self::SETTING_IDENTIFIER,
                'value' => '1qaz2wsx3edc4rfv5tgb6yhn7ujm8il.',
            ]
        );
        $coreSettingService->createSetting($settingCreateStruct);
    }
}
