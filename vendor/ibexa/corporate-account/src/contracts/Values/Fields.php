<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\CorporateAccount\Values;

use Ibexa\Contracts\Core\Repository\Values\Content\Field;

trait Fields
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Field[] */
    public array $fields = [];

    /** @param mixed $value */
    public function setField(
        string $fieldDefIdentifier,
        $value,
        ?string $language = null
    ): void {
        $this->fields[] = new Field(
            [
                'fieldDefIdentifier' => $fieldDefIdentifier,
                'value' => $value,
                'languageCode' => $language,
            ]
        );
    }
}
