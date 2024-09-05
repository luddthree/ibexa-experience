<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\Event;

use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition;
use Ibexa\PageBuilder\Data\Block\BlockConfiguration;
use Ibexa\PageBuilder\View\BlockConfigurationView;
use Symfony\Component\Form\FormInterface;
use Symfony\Contracts\EventDispatcher\Event;

class BlockConfigurationViewEvent extends Event
{
    /** @var \Ibexa\PageBuilder\View\BlockConfigurationView */
    protected $blockConfigurationView;

    /** @var \Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition */
    protected $blockDefinition;

    /** @var \Ibexa\PageBuilder\Data\Block\BlockConfiguration */
    protected $blockConfiguration;

    /** @var \Symfony\Component\Form\FormInterface */
    protected $blockConfigurationForm;

    /**
     * BlockConfigurationViewEvent constructor.
     *
     * @param \Ibexa\PageBuilder\View\BlockConfigurationView $blockConfigurationView
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition $blockDefinition
     * @param \Ibexa\PageBuilder\Data\Block\BlockConfiguration $blockConfiguration
     * @param \Symfony\Component\Form\FormInterface $blockConfigurationForm
     */
    public function __construct(
        BlockConfigurationView $blockConfigurationView,
        BlockDefinition $blockDefinition,
        BlockConfiguration $blockConfiguration,
        FormInterface $blockConfigurationForm
    ) {
        $this->blockConfigurationView = $blockConfigurationView;
        $this->blockDefinition = $blockDefinition;
        $this->blockConfiguration = $blockConfiguration;
        $this->blockConfigurationForm = $blockConfigurationForm;
    }

    /**
     * @return \Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition
     */
    public function getBlockDefinition(): BlockDefinition
    {
        return $this->blockDefinition;
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition $blockDefinition
     */
    public function setBlockDefinition(BlockDefinition $blockDefinition): void
    {
        $this->blockDefinition = $blockDefinition;
    }

    /**
     * @return \Ibexa\PageBuilder\View\BlockConfigurationView
     */
    public function getBlockConfigurationView(): BlockConfigurationView
    {
        return $this->blockConfigurationView;
    }

    /**
     * @param \Ibexa\PageBuilder\View\BlockConfigurationView $blockConfigurationView
     */
    public function setBlockConfigurationView(BlockConfigurationView $blockConfigurationView): void
    {
        $this->blockConfigurationView = $blockConfigurationView;
    }

    /**
     * @return \Ibexa\PageBuilder\Data\Block\BlockConfiguration
     */
    public function getBlockConfiguration(): BlockConfiguration
    {
        return $this->blockConfiguration;
    }

    /**
     * @param \Ibexa\PageBuilder\Data\Block\BlockConfiguration $blockConfiguration
     */
    public function setBlockConfiguration(BlockConfiguration $blockConfiguration): void
    {
        $this->blockConfiguration = $blockConfiguration;
    }

    /**
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getBlockConfigurationForm(): FormInterface
    {
        return $this->blockConfigurationForm;
    }

    /**
     * @param \Symfony\Component\Form\FormInterface $blockConfigurationForm
     */
    public function setBlockConfigurationForm(FormInterface $blockConfigurationForm): void
    {
        $this->blockConfigurationForm = $blockConfigurationForm;
    }
}

class_alias(BlockConfigurationViewEvent::class, 'EzSystems\EzPlatformPageBuilder\Event\BlockConfigurationViewEvent');
