<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Permission\Limitation\Target;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

final class ApplicationStateTarget extends ValueObject
{
    private string $from;

    private string $to;

    public function __construct(
        string $from,
        string $to
    ) {
        parent::__construct();

        $this->from = $from;
        $this->to = $to;
    }

    public function getFrom(): string
    {
        return $this->from;
    }

    public function getTo(): string
    {
        return $this->to;
    }
}
