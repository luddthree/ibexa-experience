<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\UI\Completeness\Task;

use Ibexa\Bundle\ProductCatalog\UI\Completeness\Entry\EntryInterface;
use Ibexa\Bundle\ProductCatalog\UI\Completeness\Entry\PercentEntry;
use Ibexa\Bundle\ProductCatalog\UI\Completeness\Task\Subtask\TranslationSubtask;
use Ibexa\Contracts\Core\Repository\LanguageService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Contracts\Translation\TranslatorInterface;
use Traversable;

final class TranslationsTask extends AbstractTask
{
    private LanguageService $languageService;

    private TranslatorInterface $translator;

    public function __construct(
        LanguageService $languageService,
        TranslatorInterface $translator
    ) {
        $this->languageService = $languageService;
        $this->translator = $translator;
    }

    public function getIdentifier(): string
    {
        return 'translations';
    }

    public function getName(): string
    {
        return $this->translator->trans(/** @Desc("Translations") */ 'product.completeness.translations.label', [], 'ibexa_product_catalog');
    }

    public function getEntry(ProductInterface $product): ?EntryInterface
    {
        if (!$product instanceof ContentAwareProductInterface) {
            return null;
        }

        return new PercentEntry($this->getTaskCompletenessValue($product));
    }

    /**
     * @return array<\Ibexa\Bundle\ProductCatalog\UI\Completeness\Task\TaskGroup>|null
     */
    public function getSubtaskGroups(ProductInterface $product): ?array
    {
        if (!$product instanceof ContentAwareProductInterface) {
            return null;
        }

        $definedLanguages = $this->languageService->loadLanguages();
        $translations = $this->getTranslations($product->getContent());
        $defaultGroup = new TaskGroup('default', 'Default', $product);

        foreach ($definedLanguages as $language) {
            $defaultGroup->addTask(
                new TranslationSubtask($language, $translations)
            );
        }

        return [$defaultGroup];
    }

    /**
     * @phpstan-return int<1, max>
     */
    public function getWeight(): int
    {
        return 1;
    }

    public function getEditUrl(ProductInterface $product): ?string
    {
        return null;
    }

    /**
     * @return array<\Ibexa\Contracts\Core\Repository\Values\Content\Language>
     */
    private function getTranslations(Content $content): array
    {
        $translations = $this->languageService->loadLanguageListByCode(
            $content->versionInfo->languageCodes
        );

        if ($translations instanceof Traversable) {
            return iterator_to_array($translations);
        }

        return $translations;
    }
}
