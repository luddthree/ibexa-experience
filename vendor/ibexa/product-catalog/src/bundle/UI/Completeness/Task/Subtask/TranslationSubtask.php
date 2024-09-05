<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\UI\Completeness\Task\Subtask;

use Ibexa\Bundle\ProductCatalog\UI\Completeness\Entry\BooleanEntry;
use Ibexa\Bundle\ProductCatalog\UI\Completeness\Entry\EntryInterface;
use Ibexa\Bundle\ProductCatalog\UI\Completeness\Task\AbstractTask;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;

final class TranslationSubtask extends AbstractTask
{
    private Language $language;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Language[] */
    private array $translations;

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Language[] $translations
     */
    public function __construct(Language $language, array $translations)
    {
        $this->language = $language;
        $this->translations = $translations;
    }

    public function getIdentifier(): string
    {
        return $this->language->languageCode . 'translation_task';
    }

    public function getEntry(ProductInterface $product): ?EntryInterface
    {
        $translationNames = array_column($this->translations, 'name');

        return new BooleanEntry(
            in_array($this->language->name, $translationNames, true)
        );
    }

    public function getName(): string
    {
        return $this->language->name;
    }

    /**
     * @return array<\Ibexa\Bundle\ProductCatalog\UI\Completeness\Task\TaskGroup>|null
     */
    public function getSubtaskGroups(ProductInterface $product): ?array
    {
        return null;
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
}
