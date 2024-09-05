<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Request\Item;

abstract class AbstractPackage
{
    protected int $contentTypeId;

    protected string $contentTypeName;

    protected string $lang;

    public function __construct(
        int $contentTypeId,
        string $contentTypeName,
        string $lang
    ) {
        $this->contentTypeId = $contentTypeId;
        $this->contentTypeName = $contentTypeName;
        $this->lang = $lang;
    }

    public function getContentTypeId(): int
    {
        return $this->contentTypeId;
    }

    public function getContentTypeName(): string
    {
        return $this->contentTypeName;
    }

    public function getLang(): string
    {
        return $this->lang;
    }
}
