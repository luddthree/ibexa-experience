<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\HtmlDiff\Operation;

abstract class AbstractOperation implements Operation
{
    /** @var int */
    private $startInOld;

    /** @var int */
    private $endInOld;

    /** @var int */
    private $startInNew;

    /** @var int */
    private $endInNew;

    public function __construct(
        int $startInOld,
        int $endInOld,
        int $startInNew,
        int $endInNew
    ) {
        $this->startInOld = $startInOld;
        $this->endInOld = $endInOld;
        $this->startInNew = $startInNew;
        $this->endInNew = $endInNew;
    }

    public function getStartInOld(): int
    {
        return $this->startInOld;
    }

    public function getEndInOld(): int
    {
        return $this->endInOld;
    }

    public function getStartInNew(): int
    {
        return $this->startInNew;
    }

    public function getEndInNew(): int
    {
        return $this->endInNew;
    }

    protected function getArraySliceWithingRange(array $array, int $min, int $max): array
    {
        return array_values(
            array_filter(
                $array,
                static function (int $key) use ($min, $max): bool {
                    return $key >= $min && $key < $max;
                },
                ARRAY_FILTER_USE_KEY
            )
        );
    }
}

class_alias(AbstractOperation::class, 'EzSystems\EzPlatformVersionComparison\HtmlDiff\Operation\AbstractOperation');
