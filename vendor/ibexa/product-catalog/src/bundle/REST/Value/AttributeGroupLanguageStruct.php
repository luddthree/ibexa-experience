<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

final class AttributeGroupLanguageStruct extends ValueObject
{
    /**
     * @var array<string>
     */
    private array $languages;

    /**
     * @param array<string> $languages
     */
    public function __construct(array $languages)
    {
        parent::__construct();

        $this->languages = $languages;
    }

    /**
     * @return array<string>
     */
    public function getLanguages(): array
    {
        return $this->languages;
    }
}
