<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\PageBuilder\Behat\Component\Blocks;

class EmbedBlock extends PageBuilderBlock
{
    public function setDefaultTestingConfiguration(string $blockName): void
    {
        $this->setInputField('Name', $blockName);
        $this->addContent('Ibexa Digital Experience Platform/Ibexa Platform');
        $this->submitForm();
    }

    public function getDefaultPreviewData(): array
    {
        return ['parameter1' => 'Ibexa Platform'];
    }

    public function getBlockType(): string
    {
        return 'Embed';
    }
}
