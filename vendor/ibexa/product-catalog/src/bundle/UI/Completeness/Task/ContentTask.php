<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\UI\Completeness\Task;

use Ibexa\Bundle\ProductCatalog\UI\Completeness\Entry\EntryInterface;
use Ibexa\Bundle\ProductCatalog\UI\Completeness\Entry\PercentEntry;
use Ibexa\Bundle\ProductCatalog\UI\Completeness\Task\Subtask\ContentFieldSubtask;
use Ibexa\Bundle\ProductCatalog\UI\Language\PreviewLanguageResolverInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Core\Helper\FieldHelper;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ContentTask extends AbstractTask
{
    private FieldHelper $fieldHelper;

    private TranslatorInterface $translator;

    private PreviewLanguageResolverInterface $languageResolver;

    public function __construct(
        FieldHelper $fieldHelper,
        TranslatorInterface $translator,
        PreviewLanguageResolverInterface $languageResolver
    ) {
        $this->fieldHelper = $fieldHelper;
        $this->translator = $translator;
        $this->languageResolver = $languageResolver;
    }

    public function getIdentifier(): string
    {
        return 'content';
    }

    public function getName(): string
    {
        return $this->translator->trans(/** @Desc("Content") */ 'product.completeness.content.label', [], 'ibexa_product_catalog');
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

        $defaultGroup = new TaskGroup('default', 'Default', $product);
        $content = $product->getContent();
        $language = $this->languageResolver->resolve();
        $languageCode = ($language === null) ? null : $language->languageCode;

        foreach ($this->getContentFields($content, $languageCode) as $field) {
            $defaultGroup->addTask(
                new ContentFieldSubtask($content, $field, $this->fieldHelper, $languageCode)
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
     * @return iterable<\Ibexa\Contracts\Core\Repository\Values\Content\Field>
     */
    private function getContentFields(Content $content, ?string $languageCode): iterable
    {
        $contentType = $content->getContentType();
        $fields = $content->getFieldsByLanguage($languageCode);

        foreach ($fields as $field) {
            $fieldDefinition = $contentType->getFieldDefinition($field->fieldDefIdentifier);
            $isFieldRequired = $fieldDefinition->isRequired ?? false;

            if (!$isFieldRequired) {
                yield $field;
            }
        }
    }
}
