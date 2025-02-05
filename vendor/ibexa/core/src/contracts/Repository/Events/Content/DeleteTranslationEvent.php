<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\Content;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;

final class DeleteTranslationEvent extends AfterEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo */
    private $contentInfo;

    private $languageCode;

    public function __construct(
        ContentInfo $contentInfo,
        $languageCode
    ) {
        $this->contentInfo = $contentInfo;
        $this->languageCode = $languageCode;
    }

    public function getContentInfo(): ContentInfo
    {
        return $this->contentInfo;
    }

    public function getLanguageCode()
    {
        return $this->languageCode;
    }
}

class_alias(DeleteTranslationEvent::class, 'eZ\Publish\API\Repository\Events\Content\DeleteTranslationEvent');
