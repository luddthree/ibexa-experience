<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\VersionComparison\Form\ChoiceList\Loader;

use Ibexa\Bundle\VersionComparison\Form\Data\ValueChoice;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\LanguageService;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\VersionComparison\UI\VersionStatus;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\ChoiceList\Loader\AbstractChoiceLoader;
use Symfony\Contracts\Translation\TranslatorInterface;

class VersionInfoChoiceLoader extends AbstractChoiceLoader
{
    private ContentService $contentService;

    private VersionStatus$versionStatus;

    private TranslatorInterface $translator;

    private ContentInfo $contentInfo;

    private LanguageService $languageService;

    private ?Language $language;

    /**
     * @var array<string, string>
     */
    private array $languageMap = [];

    public function __construct(
        ContentService $contentService,
        VersionStatus $versionStatus,
        TranslatorInterface $translator,
        LanguageService $languageService,
        ContentInfo $contentInfo,
        ?Language $language
    ) {
        $this->contentService = $contentService;
        $this->versionStatus = $versionStatus;
        $this->translator = $translator;
        $this->contentInfo = $contentInfo;
        $this->languageService = $languageService;
        $this->language = $language;
    }

    public function loadChoices(): array
    {
        $choices = [];
        $map = $this->getLanguageMap($this->languageService->loadLanguages());

        $versions = $this->contentService->loadVersions($this->contentInfo);
        foreach ($versions as $version) {
            foreach ($version->languageCodes as $languageCode) {
                if ($this->language && $languageCode !== $this->language->languageCode) {
                    continue;
                }
                $label = $this->buildSelectLabel(
                    $version,
                    $map[$languageCode]
                );

                $choices[$label] = (string)(new ValueChoice($version->versionNo, $languageCode));
            }
        }

        return $choices;
    }

    protected function buildSelectLabel(VersionInfo $versionInfo, string $languageName): string
    {
        return $this->translator->trans(
            /** @Desc("%status% - Version %version% - %language%") */
            'version_info.comparison.select_name',
            [
                '%status%' => $this->versionStatus->translateStatus($versionInfo->status),
                '%version%' => $versionInfo->versionNo,
                '%language%' => $languageName,
            ],
            'ibexa_fieldtypes_comparison_ui'
        );
    }

    /**
     * @param array<\Ibexa\Contracts\Core\Repository\Values\Content\Language> $availableLanguages
     *
     * @return array<string, string>
     */
    private function getLanguageMap(array $availableLanguages): array
    {
        if (!empty($this->languageMap)) {
            return $this->languageMap;
        }

        foreach ($availableLanguages as $language) {
            $this->languageMap[$language->languageCode] = $language->name;
        }

        return $this->languageMap;
    }
}

class_alias(VersionInfoChoiceLoader::class, 'EzSystems\EzPlatformVersionComparisonBundle\Form\ChoiceList\Loader\VersionInfoChoiceLoader');
