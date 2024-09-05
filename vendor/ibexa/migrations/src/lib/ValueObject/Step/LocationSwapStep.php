<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Step;

use Ibexa\Migration\ValueObject\Location\Matcher;

final class LocationSwapStep implements StepInterface
{
    public Matcher $match1;

    public Matcher $match2;

    public function __construct(Matcher $match1, Matcher $match2)
    {
        $this->match1 = $match1;
        $this->match2 = $match2;
    }
}
