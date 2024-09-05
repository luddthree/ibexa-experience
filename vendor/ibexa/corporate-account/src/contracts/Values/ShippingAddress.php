<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\CorporateAccount\Values;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;

final class ShippingAddress extends ValueObject
{
    private Content $content;

    public function __construct(Content $content)
    {
        parent::__construct();

        $this->content = $content;
    }

    public function getName(): ?string
    {
        return $this->content->getName();
    }

    public function getId(): int
    {
        return $this->content->id;
    }

    public function getContent(): Content
    {
        return $this->content;
    }
}
