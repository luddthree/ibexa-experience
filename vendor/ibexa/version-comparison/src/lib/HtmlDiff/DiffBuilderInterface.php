<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\HtmlDiff;

interface DiffBuilderInterface
{
    public function build(string $oldVersion, string $newVersion): string;

    /**
     * @param \Ibexa\VersionComparison\HtmlDiff\Token\Token[] $oldTokens
     * @param \Ibexa\VersionComparison\HtmlDiff\Token\Token[] $newTokens
     *
     * @return \Ibexa\VersionComparison\HtmlDiff\Operation\AbstractOperation[]
     */
    public function getOperations(array $oldTokens, array $newTokens): array;
}

class_alias(DiffBuilderInterface::class, 'EzSystems\EzPlatformVersionComparison\HtmlDiff\DiffBuilderInterface');
