<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Result\Value\Diff;

class BinaryFileDiff extends AbstractDiffValue
{
    /** @var string|null */
    private $id;

    /** @var string|null */
    private $path;

    public function __construct(
        string $status,
        ?string $id = null,
        ?string $path = null
    ) {
        $this->status = $status;
        $this->id = $id;
        $this->path = $path;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }
}

class_alias(BinaryFileDiff::class, 'EzSystems\EzPlatformVersionComparison\Result\Value\Diff\BinaryFileDiff');
