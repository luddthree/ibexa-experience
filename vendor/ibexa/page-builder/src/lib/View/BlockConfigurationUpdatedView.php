<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\View;

use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition;
use Ibexa\Core\MVC\Symfony\View\BaseView;

class BlockConfigurationUpdatedView extends BaseView
{
    /** @var \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue */
    public $blockValue;

    /** @var \Ibexa\FieldTypePage\FieldType\LandingPage\Model\BlockType */
    public $blockType;

    /**
     * @return \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue
     */
    public function getBlockValue(): BlockValue
    {
        return $this->blockValue;
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue $blockValue
     *
     * @return self
     */
    public function setBlockValue(BlockValue $blockValue): self
    {
        $this->blockValue = $blockValue;

        return $this;
    }

    /**
     * @return \Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition
     */
    public function getBlockType(): BlockDefinition
    {
        return $this->blockType;
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition $blockType
     *
     * @return self
     */
    public function setBlockType(BlockDefinition $blockType): self
    {
        $this->blockType = $blockType;

        return $this;
    }
}

class_alias(BlockConfigurationUpdatedView::class, 'EzSystems\EzPlatformPageBuilder\View\BlockConfigurationUpdatedView');
