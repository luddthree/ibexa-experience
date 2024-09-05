<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\FieldType\Field;

use Ibexa\FormBuilder\Exception\FieldHandlerNotFoundException;

class FieldHandlerRegistry
{
    /** @var \Ibexa\FormBuilder\FieldType\Field\FieldHandlerInterface[] */
    private $handlers;

    /**
     * @param iterable<\Ibexa\FormBuilder\FieldType\Field\FieldHandlerInterface>|null $handlers
     */
    public function __construct(?iterable $handlers = null)
    {
        $this->handlers = [];
        if ($handlers !== null) {
            foreach ($handlers as $handler) {
                $this->handlers[$handler->getSupportedField()] = $handler;
            }
        }
    }

    /**
     * @return iterable
     */
    public function getHandlers(): iterable
    {
        return array_values($this->handlers);
    }

    /**
     * @param \Ibexa\FormBuilder\FieldType\Field\FieldHandlerInterface $handler
     */
    public function addHandler(FieldHandlerInterface $handler): void
    {
        $this->handlers[$handler->getSupportedField()] = $handler;
    }

    /**
     * @param string $identifier
     *
     * @return bool
     */
    public function hasHandler(string $identifier): bool
    {
        return isset($this->handlers[$identifier]);
    }

    /**
     * @param string $identifier
     *
     * @return \Ibexa\FormBuilder\FieldType\Field\FieldHandlerInterface
     *
     * @throws \Ibexa\FormBuilder\Exception\FieldMapperNotFoundException
     */
    public function getHandler(string $identifier): FieldHandlerInterface
    {
        if ($this->hasHandler($identifier)) {
            return $this->handlers[$identifier];
        }

        throw new FieldHandlerNotFoundException($identifier);
    }
}

class_alias(FieldHandlerRegistry::class, 'EzSystems\EzPlatformFormBuilder\FieldType\Field\FieldHandlerRegistry');
