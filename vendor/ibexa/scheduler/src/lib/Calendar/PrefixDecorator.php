<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Scheduler\Calendar;

/**
 * @internal
 */
final class PrefixDecorator
{
    /** @var string */
    private $prefix;

    public function __construct(string $prefix)
    {
        $this->prefix = $prefix . ':';
    }

    public function isDecorated(string $id): bool
    {
        return strpos($id, $this->prefix) === 0;
    }

    public function decorate(string $id): string
    {
        return $this->prefix . $id;
    }

    public function undecorate(string $id): string
    {
        return substr($id, \strlen($this->prefix));
    }
}

class_alias(PrefixDecorator::class, 'EzSystems\DateBasedPublisher\Core\Calendar\PrefixDecorator');
