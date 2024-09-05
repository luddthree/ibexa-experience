<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\ContentType;

use ArrayIterator;
use Countable;
use Ibexa\Migration\ValueObject\ValueObjectInterface;
use IteratorAggregate;
use Traversable;

/**
 * @implements IteratorAggregate<\Ibexa\Migration\ValueObject\ContentType\FieldDefinition>
 */
final class FieldDefinitionCollection implements ValueObjectInterface, Countable, IteratorAggregate
{
    /** @var \Ibexa\Migration\ValueObject\ContentType\FieldDefinition[] */
    private $fieldDefinitions = [];

    private function __construct()
    {
    }

    /**
     * @param \Ibexa\Migration\ValueObject\ContentType\FieldDefinition[] $fieldDefinitions
     */
    public static function create(array $fieldDefinitions): self
    {
        $vo = new self();

        foreach ($fieldDefinitions as $fieldDefinition) {
            $vo->addFieldDefinition($fieldDefinition);
        }

        return $vo;
    }

    private function addFieldDefinition(FieldDefinition $fieldDefinition): void
    {
        $this->fieldDefinitions[] = clone $fieldDefinition;
    }

    /**
     * @return \Ibexa\Migration\ValueObject\ContentType\FieldDefinition[]
     */
    public function getFieldDefinitions(): array
    {
        return $this->fieldDefinitions;
    }

    public function count(): int
    {
        return \count($this->fieldDefinitions);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->fieldDefinitions);
    }
}

class_alias(FieldDefinitionCollection::class, 'Ibexa\Platform\Migration\ValueObject\ContentType\FieldDefinitionCollection');
