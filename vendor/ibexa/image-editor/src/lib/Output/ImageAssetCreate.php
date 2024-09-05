<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ImageEditor\Output;

class ImageAssetCreate
{
    /** @var int */
    public $contentId;

    /** @var int */
    public $locationId;

    /** @var string */
    public $translatedName;

    public function __construct(int $contentId, int $locationId, string $translatedName)
    {
        $this->contentId = $contentId;
        $this->locationId = $locationId;
        $this->translatedName = $translatedName;
    }
}

class_alias(ImageAssetCreate::class, 'Ibexa\Platform\ImageEditor\Output\ImageAssetCreate');
