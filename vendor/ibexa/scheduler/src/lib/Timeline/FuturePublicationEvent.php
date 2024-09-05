<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Scheduler\Timeline;

use DateTimeInterface;
use Ibexa\Contracts\PageBuilder\PageBuilder\Timeline\BaseEvent;

class FuturePublicationEvent extends BaseEvent
{
    /** @var int */
    private $futureVersionNo;

    /**
     * @param string $name
     * @param string $description
     * @param \DateTimeInterface $date
     * @param string $icon
     * @param int $futureVersionNo
     */
    public function __construct(
        string $name,
        string $description,
        DateTimeInterface $date,
        string $icon,
        int $futureVersionNo
    ) {
        parent::__construct($name, $description, $date, $icon);

        $this->futureVersionNo = $futureVersionNo;
    }

    /**
     * @return int
     */
    public function getFutureVersionNo(): int
    {
        return $this->futureVersionNo;
    }

    /**
     * @param int $futureVersionNo
     */
    public function setFutureVersionNo(int $futureVersionNo): void
    {
        $this->futureVersionNo = $futureVersionNo;
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return 'future_publication';
    }
}

class_alias(FuturePublicationEvent::class, 'EzSystems\DateBasedPublisher\Core\Timeline\FuturePublicationEvent');
