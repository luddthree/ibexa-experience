<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\HtmlDiff\Token\Wrapper;

final class Note
{
    /** @var bool */
    private $isWrappable;

    /** @var bool */
    private $tagInserted;

    public function __construct(bool $isWrappable, bool $tagInserted)
    {
        $this->isWrappable = $isWrappable;
        $this->tagInserted = $tagInserted;
    }

    public function isWrappable(): bool
    {
        return $this->isWrappable;
    }

    public function isTagInserted(): bool
    {
        return $this->tagInserted;
    }
}

class_alias(Note::class, 'EzSystems\EzPlatformVersionComparison\HtmlDiff\Token\Wrapper\Note');
