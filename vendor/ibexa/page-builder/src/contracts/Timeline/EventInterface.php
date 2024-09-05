<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\PageBuilder\Timeline;

use DateTimeInterface;

interface EventInterface
{
    /**
     * @return \DateTimeInterface
     */
    public function getDate(): DateTimeInterface;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getDescription(): string;

    /**
     * @return string
     */
    public function getIcon(): string;

    /**
     * @return string
     */
    public function getType(): string;
}

class_alias(EventInterface::class, 'EzSystems\EzPlatformPageBuilder\PageBuilder\Timeline\EventInterface');
