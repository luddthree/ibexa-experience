<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Scheduler\REST\Server\Output\Generator;

/**
 * MediaType Trait.
 */
trait MediaTypeTrait
{
    protected $registered = [];

    public function registerType($name)
    {
        $this->registered[] = $name;
    }

    public function registerTypes(array $names)
    {
        $this->registered = $names;
    }

    public function isRegistered($name)
    {
        return \in_array($name, $this->registered);
    }

    /**
     * Generates a media type from $name and $type.
     *
     * @param string $name
     * @param string $type
     *
     * @return string
     */
    protected function generateMediaType($name, $type)
    {
        // @todo: [HACK] Kernel should be able to bind ValueObject type to Media-Type, but it isn't right now.
        if (!$this->isRegistered($name)) {
            return parent::generateMediaType($name, $type);
        }

        return "application/vnd.datebasedpublisher.api.{$name}+{$type}";
    }
}

class_alias(MediaTypeTrait::class, 'EzSystems\DateBasedPublisher\Core\REST\Server\Output\Generator\MediaTypeTrait');
