<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Event;

use Ibexa\Contracts\Core\Persistence\Content\FieldValue;
use Ibexa\FieldTypePage\FieldType\LandingPage\Value;
use Symfony\Contracts\EventDispatcher\Event;

class PageFromPersistenceEvent extends Event
{
    /** @var \Ibexa\Contracts\Core\Persistence\Content\FieldValue */
    private $persistenceValue;

    /** @var \Ibexa\FieldTypePage\FieldType\LandingPage\Value */
    private $value;

    /**
     * @param \Ibexa\Contracts\Core\Persistence\Content\FieldValue $persistenceValue
     * @param \Ibexa\FieldTypePage\FieldType\LandingPage\Value $value
     */
    public function __construct(FieldValue $persistenceValue, Value $value)
    {
        $this->persistenceValue = $persistenceValue;
        $this->value = $value;
    }

    /**
     * @return \Ibexa\Contracts\Core\Persistence\Content\FieldValue
     */
    public function getPersistenceValue(): FieldValue
    {
        return $this->persistenceValue;
    }

    /**
     * @param \Ibexa\Contracts\Core\Persistence\Content\FieldValue $persistenceValue
     */
    public function setPersistenceValue(FieldValue $persistenceValue): void
    {
        $this->persistenceValue = $persistenceValue;
    }

    /**
     * @return \Ibexa\FieldTypePage\FieldType\LandingPage\Value
     */
    public function getValue(): Value
    {
        return $this->value;
    }

    /**
     * @param \Ibexa\FieldTypePage\FieldType\LandingPage\Value $value
     */
    public function setValue(Value $value): void
    {
        $this->value = $value;
    }
}

class_alias(PageFromPersistenceEvent::class, 'EzSystems\EzPlatformPageFieldType\Event\PageFromPersistenceEvent');
