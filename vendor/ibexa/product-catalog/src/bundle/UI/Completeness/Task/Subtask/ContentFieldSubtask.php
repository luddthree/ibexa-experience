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
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Core\Helper\FieldHelper;

final class ContentFieldSubtask extends AbstractTask
{
    private Content $content;

    private Field $field;

    private FieldHelper $fieldHelper;

    private ?string $languageCode;

    public function __construct(
        Content $content,
        Field $field,
        FieldHelper $fieldHelper,
        ?string $languageCode
    ) {
        $this->content = $content;
        $this->field = $field;
        $this->fieldHelper = $fieldHelper;
        $this->languageCode = $languageCode;
    }

    public function getIdentifier(): string
    {
        return $this->field->id . '_content_field_task';
    }

    public function getEntry(ProductInterface $product): ?EntryInterface
    {
        $fieldDefIdentifier = $this->field->fieldDefIdentifier;

        return new BooleanEntry(
            !$this->fieldHelper->isFieldEmpty(
                $this->content,
                $fieldDefIdentifier,
                $this->languageCode
            )
        );
    }

    public function getName(): string
    {
        $contentType = $this->content->getContentType();
        $fieldDefinition = $contentType->getFieldDefinition(
            $this->field->fieldDefIdentifier
        );

        if ($fieldDefinition === null) {
            return '';
        }

        return $fieldDefinition->getName() ?? '';
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
