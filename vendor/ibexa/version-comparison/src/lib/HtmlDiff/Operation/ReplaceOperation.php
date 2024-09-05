<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\HtmlDiff\Operation;

use Ibexa\VersionComparison\HtmlDiff\Token\TokenWrapper;

final class ReplaceOperation implements Operation
{
    /** @var \Ibexa\VersionComparison\HtmlDiff\Operation\DeleteOperation */
    private $deleteOperation;

    /** @var \Ibexa\VersionComparison\HtmlDiff\Operation\InsertOperation */
    private $insertOperation;

    public function __construct(DeleteOperation $deleteOperation, InsertOperation $insertOperation)
    {
        $this->deleteOperation = $deleteOperation;
        $this->insertOperation = $insertOperation;
    }

    public function wrap(TokenWrapper $tokenWrapper, array $oldTokens, array $newTokens, string $tagClass = 'diffmod'): string
    {
        $replacedContent = '';
        $replacedContent .= $this->deleteOperation->wrap($tokenWrapper, $oldTokens, $newTokens, $tagClass);
        $replacedContent .= $this->insertOperation->wrap($tokenWrapper, $oldTokens, $newTokens, $tagClass);

        return $replacedContent;
    }
}

class_alias(ReplaceOperation::class, 'EzSystems\EzPlatformVersionComparison\HtmlDiff\Operation\ReplaceOperation');
