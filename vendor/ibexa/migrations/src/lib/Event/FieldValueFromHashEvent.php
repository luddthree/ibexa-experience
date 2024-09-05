<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Event;

use Symfony\Contracts\EventDispatcher\Event;

final class FieldValueFromHashEvent extends Event
{
    /** @var string */
    private $fieldTypeIdentifier;

    /** @var mixed|null Data from external storage, might be `null` if value is empty */
    private $hash;

    /** @var array<string, mixed> */
    private $fieldTypeSettings;

    /**
     * @param array<string, mixed> $fieldTypeSettings
     * @param mixed|null $hash
     */
    public function __construct(string $fieldTypeIdentifier, array $fieldTypeSettings, $hash)
    {
        $this->fieldTypeIdentifier = $fieldTypeIdentifier;
        $this->hash = $hash;
        $this->fieldTypeSettings = $fieldTypeSettings;
    }

    public function getFieldTypeIdentifier(): string
    {
        return $this->fieldTypeIdentifier;
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

    /**
     * @return array<string, mixed>
     */
    public function getFieldTypeSettings(): array
    {
        return $this->fieldTypeSettings;
    }
}

class_alias(FieldValueFromHashEvent::class, 'Ibexa\Platform\Migration\Event\FieldValueFromHashEvent');
