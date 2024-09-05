<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Rest\Server\Values\RestContentCreateStruct;

class ProductCreateStruct extends ValueObject
{
    private RestContentCreateStruct $contentCreateStruct;

    private ?string $code;

    /** @var array<string,mixed> */
    private array $attributes;

    /**
     * @param array<string,mixed> $attributes
     */
    public function __construct(
        RestContentCreateStruct $contentCreateStruct,
        ?string $code = null,
        array $attributes = []
    ) {
        parent::__construct();

        $this->contentCreateStruct = $contentCreateStruct;
        $this->code = $code;
        $this->attributes = $attributes;
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

    public function getContentCreateStruct(): RestContentCreateStruct
    {
        return $this->contentCreateStruct;
    }
}
