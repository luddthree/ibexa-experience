<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Seo\FieldType;

use Ibexa\Contracts\Seo\Value\ValueInterface;
use Ibexa\Core\FieldType\Value;
use Ibexa\Seo\Value\SeoTypesValue;

final class SeoValue extends Value
{
    public ?SeoTypesValue $seoTypesValue = null;

    public function __construct(?SeoTypesValue $value = null)
    {
        parent::__construct();
        $this->seoTypesValue = $value;
    }

    public function __toString(): string
    {
        return (string)$this->seoTypesValue;
    }

    public function getSeoTypesValue(): ?ValueInterface
    {
        return $this->seoTypesValue;
    }
}
