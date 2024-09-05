<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject;

use InvalidArgumentException;

abstract class AbstractMatcher
{
    /** @var string */
    public $field;

    /** @var scalar */
    public $value;

    /**
     * @param scalar $value
     */
    public function __construct(string $field, $value)
    {
        $this->guardName($field);

        $this->field = $field;
        $this->value = $value;
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function guardName(string $field): void
    {
        if (false === array_key_exists($field, $this->getSupportedFields())) {
            throw new InvalidArgumentException(sprintf(
                'Unknown field name: %s. Supported fields: [%s]',
                $field,
                implode('|', array_keys($this->getSupportedFields()))
            ));
        }
    }

    /**
     * @return array<string, string>
     */
    abstract protected function getSupportedFields(): array;
}

/**
 * @deprecated Use Ibexa\Migration\ValueObject\AbstractMatcher instead
 */
class_alias(AbstractMatcher::class, 'Ibexa\Platform\Migration\ValueObject\AbstractMatch');

class_alias(AbstractMatcher::class, 'Ibexa\Platform\Migration\ValueObject\AbstractMatcher');
