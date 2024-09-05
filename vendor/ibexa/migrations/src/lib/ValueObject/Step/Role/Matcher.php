<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Step\Role;

use function array_key_exists;
use function array_keys;
use function implode;
use InvalidArgumentException;
use function sprintf;

final class Matcher
{
    public const IDENTIFIER = 'identifier';
    public const ID = 'id';

    private const SUPPORTED_FIELDS = [
        self::IDENTIFIER => self::IDENTIFIER,
        self::ID => self::ID,
    ];

    /** @var string */
    public $field;

    /** @var mixed */
    public $value;

    /**
     * @param mixed $value
     */
    public function __construct(string $field, $value)
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

class_alias(Matcher::class, 'Ibexa\Platform\Migration\ValueObject\Step\Role\Matcher');
