<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\View;

use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition;
use Ibexa\Core\MVC\Symfony\View\BaseView;
use Symfony\Component\Form\FormView;

class BlockConfigurationView extends BaseView
{
    public string $blockTypeIdentifier;

    public BlockDefinition $blockType;

    public FormView $form;

    public function setParameter($parameter, $value)
    {
        $this->parameters[$parameter] = $value;
    }

    public function getBlockTypeIdentifier(): string
    {
        return $this->blockTypeIdentifier;
    }

    public function setBlockTypeIdentifier(string $blockTypeIdentifier): self
    {
        $this->blockTypeIdentifier = $blockTypeIdentifier;

        return $this;
    }

    public function getBlockType(): BlockDefinition
    {
        return $this->blockType;
    }

    public function setBlockType(BlockDefinition $blockType): self
    {
        $this->blockType = $blockType;

        return $this;
    }

    public function getForm(): FormView
    {
        return $this->form;
    }

    public function setForm(FormView $form): self
    {
        $this->form = $form;

        return $this;
    }
}

class_alias(BlockConfigurationView::class, 'EzSystems\EzPlatformPageBuilder\View\BlockConfigurationView');
