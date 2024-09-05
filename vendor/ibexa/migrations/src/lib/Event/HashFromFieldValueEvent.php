<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Event;

use Ibexa\Contracts\Core\FieldType\Value;
use Symfony\Contracts\EventDispatcher\Event;

final class HashFromFieldValueEvent extends Event
{
    /** @var \Ibexa\Contracts\Core\FieldType\Value */
    private $spiFieldTypeValue;

    /** @var array|null Data from external storage, might be `null` if value is empty */
    private $hash;

    /**
     * @param mixed|null $hash
     */
    public function __construct(Value $persistenceValue, $hash = null)
    {
        $this->spiFieldTypeValue = $persistenceValue;
        $this->hash = $hash;
    }

    public function getSpiFieldTypeValue(): Value
    {
        return $this->spiFieldTypeValue;
    }

    /**
     * @return mixed|null
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param mixed|null $hash
     */
    public function setHash($hash): void
    {
        $this->hash = $hash;
    }
}

class_alias(HashFromFieldValueEvent::class, 'Ibexa\Platform\Migration\Event\HashFromFieldValueEvent');
