<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypeAddress\FieldType;

use Ibexa\Core\FieldType\Value as BaseValue;
use Symfony\Component\Intl\Countries;

final class Value extends BaseValue
{
    public const FIELDS_IDENTIFIER = 'fields';

    public ?string $name = null;

    public ?string $country = null;

    public array $fields;

    public function __construct(
        ?string $name = null,
        ?string $country = null,
        array $fields = []
    ) {
        parent::__construct();

        $this->name = $name;
        $this->country = $country;
        $this->fields = $fields;
    }

    public function __toString(): string
    {
        return implode(', ', array_filter([
            $this->name,
            $this->country ? Countries::getName($this->country) : null,
        ]));
    }
}
