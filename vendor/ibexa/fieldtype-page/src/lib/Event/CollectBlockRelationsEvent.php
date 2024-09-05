<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Event;

use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Ibexa\FieldTypePage\FieldType\LandingPage\Value;
use Symfony\Contracts\EventDispatcher\Event;

class CollectBlockRelationsEvent extends Event
{
    /** @var \Ibexa\FieldTypePage\FieldType\LandingPage\Value */
    protected $fieldValue;

    /** @var \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue */
    protected $blockValue;

    /** @var int[] Array containing Content IDs */
    protected $relations;

    /**
     * @param \Ibexa\FieldTypePage\FieldType\LandingPage\Value $fieldValue
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue $blockValue
     * @param int[] $relations
     */
    public function __construct(
        Value $fieldValue,
        BlockValue $blockValue,
        array $relations = []
    ) {
        $this->fieldValue = $fieldValue;
        $this->blockValue = $blockValue;
        $this->relations = $relations;
    }

    /**
     * \Ibexa\FieldTypePage\FieldType\LandingPage\Value.
     */
    public function getFieldValue(): Value
    {
        return $this->fieldValue;
    }

    /**
     * @param \Ibexa\FieldTypePage\FieldType\LandingPage\Value $fieldValue
     */
    public function setFieldValue(Value $fieldValue): void
    {
        $this->fieldValue = $fieldValue;
    }

    /**
     * @return \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue
     */
    public function getBlockValue(): BlockValue
    {
        return $this->blockValue;
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue $blockValue
     */
    public function setBlockValue(BlockValue $blockValue): void
    {
        $this->blockValue = $blockValue;
    }

    /**
     * @return int[]
     */
    public function getRelations(): array
    {
        return $this->relations;
    }

    /**
     * @param int[] $relations
     */
    public function setRelations(array $relations): void
    {
        $this->relations = $relations;
    }
}

class_alias(CollectBlockRelationsEvent::class, 'EzSystems\EzPlatformPageFieldType\Event\CollectBlockRelationsEvent');
