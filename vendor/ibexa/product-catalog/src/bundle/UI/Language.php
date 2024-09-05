<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\UI;

use Ibexa\Contracts\Core\Repository\Values\Content\Language as APILanguage;

/**
 * Extends original value object in order to provide additional fields.
 * Takes a standard language instance and retrieves properties from it in addition to the provided properties.
 */
class Language extends APILanguage
{
    protected bool $isMain;

    protected bool $userCanRemove = false;

    /**
     * @param array<mixed> $properties
     */
    public function __construct(APILanguage $language, array $properties = [])
    {
        parent::__construct(get_object_vars($language) + $properties);
    }

    public function canDeleteTranslation(): bool
    {
        return $this->userCanRemove && !$this->isMain;
    }
}
