<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Content;

use Ibexa\Contracts\Core\Repository\Values\Content\Field as APIField;
use Webmozart\Assert\Assert;

final class Field
{
    /** @var string */
    public $fieldDefIdentifier;

    /** @var array<scalar|null>|scalar|null */
    public $value;

    /** @var string|null */
    public $languageCode;

    /**
     * @param scalar|null $value
     */
    private function __construct(string $fieldDefIdentifier, $value, ?string $languageCode)
    {
        $this->fieldDefIdentifier = $fieldDefIdentifier;
        $this->value = $value;
        $this->languageCode = $languageCode;
    }

    /**
     * @param array{
     *     fieldDefIdentifier: string,
     *     value: array<scalar|null>|scalar|null,
     *     languageCode?: ?string,
     * } $data
     */
    public static function createFromArray(array $data): self
    {
        $value = $data['value'];
        if (!is_array($value)) {
            Assert::nullOrScalar($value);
        }

        return new self(
            $data['fieldDefIdentifier'],
            $value,
            $data['languageCode'] ?? null,
        );
    }

    /**
     * @param array<scalar|null>|scalar|null $value
     */
    public static function createFromValueObject(APIField $field, $value): self
    {
        if (!is_array($value)) {
            Assert::nullOrScalar($value);
        }

        return new self(
            $field->fieldDefIdentifier,
            $value,
            $field->languageCode,
        );
    }
}

class_alias(Field::class, 'Ibexa\Platform\Migration\ValueObject\Content\Field');
