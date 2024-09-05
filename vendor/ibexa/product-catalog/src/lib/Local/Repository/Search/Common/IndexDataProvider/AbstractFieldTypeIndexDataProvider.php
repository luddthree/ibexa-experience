<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Search\Common\IndexDataProvider;

use Ibexa\Contracts\Core\Persistence\Content as SPIContent;
use Ibexa\Contracts\Core\Persistence\Content\Field;

abstract class AbstractFieldTypeIndexDataProvider implements IndexDataProviderInterface
{
    final public function isSupported(SPIContent $content): bool
    {
        return $this->findField($content) !== null;
    }

    /**
     * @return \Ibexa\Contracts\Core\Search\Field[]
     */
    final public function getSearchData(SPIContent $content): array
    {
        $field = $this->findField($content);
        if ($field === null) {
            return [];
        }

        return $this->doGetSearchData($content, $field);
    }

    abstract protected function getFieldTypeIdentifier(): string;

    /**
     * @return \Ibexa\Contracts\Core\Search\Field[]
     */
    abstract protected function doGetSearchData(SPIContent $content, Field $field): array;

    private function findField(SPIContent $content): ?Field
    {
        foreach ($content->fields as $field) {
            if ($field->type === $this->getFieldTypeIdentifier()) {
                return $field;
            }
        }

        return null;
    }
}
