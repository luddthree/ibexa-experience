<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value;

use Ibexa\Contracts\Core\Repository\Values\Content\ContentUpdateStruct;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;

class ProductUpdateStruct extends ValueObject
{
    private ?string $code;

    /** @var array<string,mixed> */
    private array $attributes;

    /**
     * The update structure for the product content.
     */
    private ContentUpdateStruct $contentUpdateStruct;

    /**
     * @param array<string,mixed> $attributes
     */
    public function __construct(
        ContentUpdateStruct $contentUpdateStruct,
        ?string $code = null,
        array $attributes = []
    ) {
        parent::__construct();

        $this->contentUpdateStruct = $contentUpdateStruct;
        $this->attributes = $attributes;
        $this->code = $code;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @return array<string,mixed>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getContentUpdateStruct(): ContentUpdateStruct
    {
        return $this->contentUpdateStruct;
    }
}
