<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\HtmlDiff\Token\Wrapper;

final class TagStackElement
{
    /** @var string */
    private $tagName;

    /** @var int */
    private $position;

    public function __construct(string $tagName, int $position)
    {
        $this->tagName = $tagName;
        $this->position = $position;
    }

    public function getTagName(): string
    {
        return $this->tagName;
    }

    public function getPosition(): int
    {
        return $this->position;
    }
}

class_alias(TagStackElement::class, 'EzSystems\EzPlatformVersionComparison\HtmlDiff\Token\Wrapper\TagStackElement');
