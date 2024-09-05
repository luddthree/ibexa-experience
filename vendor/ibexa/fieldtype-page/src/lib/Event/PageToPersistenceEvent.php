<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Event;

use Ibexa\Contracts\Core\FieldType\Value;
use Symfony\Contracts\EventDispatcher\Event;

class PageToPersistenceEvent extends Event
{
    /** @var \Ibexa\Contracts\Core\FieldType\Value */
    private $spiFieldTypeValue;

    /** @var array|null Data from external storage, might be `null` if Page is empty */
    private $value;

    /**
     * @param \Ibexa\Contracts\Core\FieldType\Value $persistenceValue
     * @param array|null $value
     */
    public function __construct(Value $persistenceValue, ?array $value = null)
    {
        $this->spiFieldTypeValue = $persistenceValue;
        $this->value = $value;
    }

    /**
     * @return \Ibexa\Contracts\Core\FieldType\Value
     */
    public function getSpiFieldTypeValue(): Value
    {
        return $this->spiFieldTypeValue;
    }

    /**
     * @param \Ibexa\Contracts\Core\FieldType\Value $spiFieldTypeValue
     */
    public function setSpiFieldTypeValue(Value $spiFieldTypeValue): void
    {
        $this->spiFieldTypeValue = $spiFieldTypeValue;
    }

    /**
     * @return array|null
     */
    public function getValue(): ?array
    {
        return $this->value;
    }

    /**
     * @param array|null $value
     */
    public function setValue(?array $value): void
    {
        $this->value = $value;
    }
}

class_alias(PageToPersistenceEvent::class, 'EzSystems\EzPlatformPageFieldType\Event\PageToPersistenceEvent');
