<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\HtmlDiff\Operation;

use Ibexa\VersionComparison\HtmlDiff\Token\TokenWrapper;

final class DeleteOperation extends AbstractOperation
{
    public function wrap(TokenWrapper $tokenWrapper, array $oldTokens, array $newTokens, string $tagClass = 'diffdel'): string
    {
        return $tokenWrapper->wrap(
            $this->getArraySliceWithingRange($oldTokens, $this->getStartInOld(), $this->getEndInOld() + 1),
            'del',
            $tagClass
        );
    }
}

class_alias(DeleteOperation::class, 'EzSystems\EzPlatformVersionComparison\HtmlDiff\Operation\DeleteOperation');
