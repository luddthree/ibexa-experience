<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\PageBuilder;

use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;

/**
 * @deprecated Since 4.5 The "PreviewLanguageCodeResolver" class is deprecated, will be removed in 5.0.
 */
final class PreviewLanguageCodeResolver
{
    public function resolveLanguageCodeForVersionInfo(VersionInfo $versionInfo, string $languageCode): string
    {
        $isAlwaysAvailable = $versionInfo->getContentInfo()->alwaysAvailable;
        if ($isAlwaysAvailable) {
            return $languageCode;
        }

        $languages = $versionInfo->getLanguages();
        $languageCodes = [];
        foreach ($languages as $language) {
            $languageCodes[] = $language->getLanguageCode();
        }

        if (!in_array($languageCode, $languageCodes)) {
            // First translation, location won't be able to be loaded, so we have to change language
            $languageCode = $versionInfo->getInitialLanguage()->getLanguageCode();
        }

        return $languageCode;
    }
}
