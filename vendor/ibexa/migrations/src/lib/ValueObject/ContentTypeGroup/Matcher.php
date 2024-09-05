<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\ContentTypeGroup;

use InvalidArgumentException;

final class Matcher
{
    public const CONTENT_TYPE_NAME_IDENTIFIER = 'identifier';

    private const SUPPORTED_FIELDS = [
        self::CONTENT_TYPE_NAME_IDENTIFIER => true,
    ];

    /** @var string */
    public $field;

    /** @var string */
    public $value;

    public function __construct(string $field, string $value)
    {
        $this->guardName($field);

        $this->field = $field;
        $this->value = $value;
    }

    private function guardName(string $field): void
    {
        if (false === array_key_exists($field, self::SUPPORTED_FIELDS)) {
            throw new InvalidArgumentException(sprintf(
                'Unknown field name: %s. Supported fields: [%s]',
                $field,
                implode('|', array_keys(self::SUPPORTED_FIELDS))
            ));
        }
    }
}

/**
 * @deprecated Use Ibexa\Migration\ValueObject\ContentTypeGroup\Matcher class instead
 */
class_alias(Matcher::class, 'Ibexa\Platform\Migration\ValueObject\ContentTypeGroup\Match');

class_alias(Matcher::class, 'Ibexa\Platform\Migration\ValueObject\ContentTypeGroup\Matcher');
