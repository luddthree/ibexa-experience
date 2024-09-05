<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FieldTypePage\Persistence;

use Ibexa\Contracts\Core\Persistence\ValueObject;

class BlockEntry extends ValueObject
{
    /** @var int */
    public $id;

    /** @var int */
    public $userId;

    /** @var int */
    public $contentId;

    /** @var int */
    public $versionNumber;

    /** @var int */
    public $actionTimestamp;

    /** @var string */
    public $blockName;

    /** @var string */
    public $blockType;
}

class_alias(BlockEntry::class, 'EzSystems\EzPlatformPageFieldType\Persistence\BlockEntry');
