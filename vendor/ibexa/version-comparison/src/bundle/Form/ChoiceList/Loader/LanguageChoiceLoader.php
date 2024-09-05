<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\VersionComparison\Form\ChoiceList\Loader;

use Ibexa\AdminUi\Form\Type\ChoiceList\Loader\ConfiguredLanguagesChoiceLoader;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Symfony\Component\Form\ChoiceList\Loader\AbstractChoiceLoader;

final class LanguageChoiceLoader extends AbstractChoiceLoader
{
    private ConfiguredLanguagesChoiceLoader $languageChoiceLoader;

    private ContentService $contentService;

    private ContentInfo $contentInfo;

    public function __construct(
        ConfiguredLanguagesChoiceLoader $loader,
        ContentService $contentService,
        ContentInfo $contentInfo
    ) {
        $this->languageChoiceLoader = $loader;
        $this->contentService = $contentService;
        $this->contentInfo = $contentInfo;
    }

    public function loadChoices(): array
    {
        $versions = $this->contentService->loadVersions($this->contentInfo);
        $contentLanguageCodes = array_unique(
            array_reduce(
                array_column($versions, 'languageCodes'),
                'array_merge',
                []
            )
        );
        $availableLanguages = $this->languageChoiceLoader->getChoiceList();
        $languages = [];
        foreach ($availableLanguages as $language) {
            if (in_array($language->getLanguageCode(), $contentLanguageCodes, true)) {
                $languages[] = $language;
            }
        }

        return $languages;
    }
}
