<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\FieldType\Page\Block\Attribute\ValueConverter;

use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Attribute\ValueConverter\ValueConverterInterface;

class ContentTypeList implements ValueConverterInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\ContentTypeService */
    private $contentTypeService;

    /**
     * @param \Ibexa\Contracts\Core\Repository\ContentTypeService $contentTypeService
     */
    public function __construct(ContentTypeService $contentTypeService)
    {
        $this->contentTypeService = $contentTypeService;
    }

    /**
     * @param string $storageValue
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType[]
     */
    public function fromStorageValue(string $storageValue): array
    {
        return array_map([$this->contentTypeService, 'loadContentTypeByIdentifier'], explode(',', $storageValue));
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType[] $value
     *
     * @return string
     */
    public function toStorageValue($value): string
    {
        return implode(',', array_column($value, 'identifier'));
    }
}

class_alias(ContentTypeList::class, 'EzSystems\EzPlatformPageFieldType\FieldType\Page\Block\Attribute\ValueConverter\ContentTypeList');
