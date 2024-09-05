<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\HtmlDiff\Operation;

use Ibexa\VersionComparison\HtmlDiff\Token\TokenWrapper;

final class InsertOperation extends AbstractOperation
{
    public function wrap(TokenWrapper $tokenWrapper, array $oldTokens, array $newTokens, string $tagClass = 'diffins'): string
    {
        return $tokenWrapper->wrap(
            $this->getArraySliceWithingRange($newTokens, $this->getStartInNew(), $this->getEndInNew() + 1),
            'ins',
            $tagClass
        );
    }
}

class_alias(InsertOperation::class, 'EzSystems\EzPlatformVersionComparison\HtmlDiff\Operation\InsertOperation');
