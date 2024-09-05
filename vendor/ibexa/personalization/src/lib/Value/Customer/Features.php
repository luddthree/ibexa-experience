<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Customer;

final class Features
{
    private const VARIANTS_SUPPORTED_VALUE = 'DUPLICATE_EVENT_FROM_VARIANT';

    private bool $isVariantSupported;

    private function __construct(
        bool $isVariantSupported
    ) {
        $this->isVariantSupported = $isVariantSupported;
    }

    /**
     * @param string[] $parameters
     */
    public static function fromArray(array $parameters): self
    {
        $isVariantSupported = in_array(self::VARIANTS_SUPPORTED_VALUE, $parameters, true);

        return new self(
            $isVariantSupported
        );
    }

    public function isVariantSupported(): bool
    {
        return $this->isVariantSupported;
    }
}
