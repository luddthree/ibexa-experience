<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Search\Common\FieldNameBuilder;

final class AttributeFieldNameBuilder
{
    private const SEARCH_FIELD_PREFIX = 'product_attribute';
    private const SEARCH_FIELD_IS_NULL_SUFFIX = 'is_null';

    private string $identifier;

    private string $field;

    public function __construct(string $identifier)
    {
        $this->withIdentifier($identifier);
    }

    /**
     * @return $this
     */
    public function withIdentifier(string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * @return $this
     */
    public function withField(string $field): self
    {
        $this->field = $field;

        return $this;
    }

    /**
     * @return $this
     */
    public function withIsNull(): self
    {
        return $this->withField(self::SEARCH_FIELD_IS_NULL_SUFFIX);
    }

    /**
     * @return non-empty-string
     */
    public function build(): string
    {
        if (!isset($this->field)) {
            throw new \LogicException('Missing field');
        }

        return implode('_', [
            self::SEARCH_FIELD_PREFIX,
            $this->identifier,
            $this->field,
        ]);
    }
}
